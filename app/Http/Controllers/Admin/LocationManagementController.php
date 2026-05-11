<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocationManagementController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalMunicipalities = DB::table('municipalities')->count();
        $totalBarangays = DB::table('barangays')->count();
        $pendingRequests = DB::table('municipality_requests')
            ->where('status', 'pending')
            ->count() + DB::table('barangay_requests')
            ->where('status', 'pending')
            ->count();
        
        $approvedToday = DB::table('municipality_requests')
            ->where('status', 'approved')
            ->whereDate('approved_at', today())
            ->count() + DB::table('barangay_requests')
            ->where('status', 'approved')
            ->whereDate('approved_at', today())
            ->count();
        
        return view('admin.locations.index', compact(
            'totalMunicipalities',
            'totalBarangays',
            'pendingRequests',
            'approvedToday'
        ));
    }
    
    public function getRequestsData(Request $request)
    {
        $status = $request->get('status', '');
        $type = $request->get('type', '');
        $dateFrom = $request->get('dateFrom');
        $dateTo = $request->get('dateTo');
        $search = $request->get('search', '');
        
        // Use location_requests table (from staff location requests)
        $query = DB::table('location_requests as lr')
            ->join('users as u', 'u.id', '=', 'lr.requested_by')
            ->leftJoin('users as ru', 'ru.id', '=', 'lr.reviewed_by')
            ->leftJoin('municipalities as m', 'm.id', '=', 'lr.municipality_id')
            ->select(
                'lr.*',
                'u.first_name as requested_by_firstname',
                'u.last_name as requested_by_lastname',
                'ru.first_name as reviewed_by_firstname',
                'ru.last_name as reviewed_by_lastname',
                'm.name as municipality_name',
                DB::raw("'municipality' as type")
            )
            ->where('lr.request_type', 'municipality');
        
        $barangayQuery = DB::table('location_requests as lr')
            ->join('users as u', 'u.id', '=', 'lr.requested_by')
            ->leftJoin('users as ru', 'ru.id', '=', 'lr.reviewed_by')
            ->leftJoin('municipalities as m', 'm.id', '=', 'lr.municipality_id')
            ->select(
                'lr.*',
                'u.first_name as requested_by_firstname',
                'u.last_name as requested_by_lastname',
                'ru.first_name as reviewed_by_firstname',
                'ru.last_name as reviewed_by_lastname',
                'm.name as municipality_name',
                DB::raw("'barangay' as type")
            )
            ->where('lr.request_type', 'barangay');
        
        // Combine queries
        if ($type === 'municipality' || $type === '') {
            $query->where('lr.status', '!=', 'deleted');
        } else {
            $query->where('lr.status', '!=', 'deleted');
        }
        
        if ($status) {
            $query->where('lr.status', $status);
        }
        
        if ($dateFrom) {
            $query->whereDate('lr.created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('lr.created_at', '<=', $dateTo);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('lr.name', 'like', "%{$search}%")
                  ->orWhere('m.name', 'like', "%{$search}%");
            });
        }
        
        $requests = $query->orderByDesc('lr.created_at')->get();
        
        return response()->json(['requests' => $requests]);
    }
    
    public function approve($type, $id)
    {
        try {
            DB::beginTransaction();
            
            $request = DB::table('location_requests')->where('id', $id)->first();
            
            if (!$request) {
                return response()->json(['error' => 'Request not found'], 404);
            }
            
            if ($request->request_type !== $type) {
                return response()->json(['error' => 'Request type mismatch'], 400);
            }
            
            if ($type === 'municipality') {
                // Create actual municipality record
                $municipalityId = DB::table('municipalities')->insertGetId([
                    'name' => $request->name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Update request status
                DB::table('location_requests')
                    ->where('id', $id)
                    ->update([
                        'status' => 'approved',
                        'municipality_id' => $municipalityId,
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                        'updated_at' => now(),
                    ]);
                
                $message = 'Municipality request approved successfully.';
                
            } elseif ($type === 'barangay') {
                // Create actual barangay record
                $barangayId = DB::table('barangays')->insertGetId([
                    'name' => $request->name,
                    'municipality_id' => $request->municipality_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Update request status
                DB::table('location_requests')
                    ->where('id', $id)
                    ->update([
                        'status' => 'approved',
                        'barangay_id' => $barangayId,
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                        'updated_at' => now(),
                    ]);
                
                $message = 'Barangay request approved successfully.';
            }
            
            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approval error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to approve request'], 500);
        }
    }
    
    public function reject(Request $request, $type, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);
        
        try {
            DB::beginTransaction();
            
            $locationRequest = DB::table('location_requests')->where('id', $id)->first();
            
            if (!$locationRequest) {
                return response()->json(['error' => 'Request not found'], 404);
            }
            
            if ($locationRequest->request_type !== $type) {
                return response()->json(['error' => 'Request type mismatch'], 400);
            }
            
            // Update request status
            DB::table('location_requests')
                ->where('id', $id)
                ->update([
                    'status' => 'rejected',
                    'rejection_reason' => $validated['rejection_reason'],
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'updated_at' => now(),
                ]);
            
            $message = $type === 'municipality' ? 'Municipality request rejected successfully.' : 'Barangay request rejected successfully.';
            
            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Rejection error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to reject request'], 500);
        }
    }
    
    public function viewDetails($type, $id)
    {
        $request = DB::table('location_requests as lr')
            ->join('users as u', 'u.id', '=', 'lr.requested_by')
            ->leftJoin('users as ru', 'ru.id', '=', 'lr.reviewed_by')
            ->leftJoin('municipalities as m', 'm.id', '=', 'lr.municipality_id')
            ->where('lr.id', $id)
            ->where('lr.request_type', $type)
            ->select(
                'lr.*',
                'u.first_name as requested_by_firstname',
                'u.last_name as requested_by_lastname',
                'ru.first_name as reviewed_by_firstname',
                'ru.last_name as reviewed_by_lastname',
                'm.name as municipality_name'
            )
            ->first();
        
        if (!$request) {
            return response()->json(['error' => 'Request not found'], 404);
        }
        
        return response()->json(['request' => $request]);
    }
}
