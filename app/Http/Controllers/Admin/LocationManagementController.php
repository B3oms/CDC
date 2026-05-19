<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Municipality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocationManagementController extends Controller
{
    public function index()
    {
        // Get all location requests with user details (for pending section)
        $locationRequests = DB::table('location_requests')
            ->leftJoin('users as requested_by', 'location_requests.requested_by', '=', 'requested_by.id')
            ->leftJoin('users as approved_by', 'location_requests.approved_by', '=', 'approved_by.id')
            ->select(
                'location_requests.*',
                'requested_by.first_name as requested_by_firstname',
                'requested_by.last_name as requested_by_lastname',
                'approved_by.first_name as approved_by_firstname',
                'approved_by.last_name as approved_by_lastname'
            )
            ->orderBy('location_requests.created_at', 'desc')
            ->get();

        // Get all actual locations from the system
        $municipalities = DB::table('municipalities')
            ->orderBy('name')
            ->get()
            ->map(function ($municipality) {
                $municipality->type = 'municipality';
                $municipality->province = $municipality->province;
                $municipality->approved_at = $municipality->created_at;
                return $municipality;
            });

        $barangays = DB::table('barangays')
            ->leftJoin('municipalities', 'barangays.municipality_id', '=', 'municipalities.id')
            ->select('barangays.*', 'municipalities.name as municipality_name', 'municipalities.province')
            ->orderBy('municipalities.name')
            ->orderBy('barangays.name')
            ->get()
            ->map(function ($barangay) {
                $barangay->type = 'barangay';
                $barangay->province = $barangay->province;
                $barangay->approved_at = $barangay->created_at;
                return $barangay;
            });

        // Combine all locations
        $allLocations = $municipalities->concat($barangays);

        // Get statistics
        $pendingRequests = $locationRequests->where('status', 'pending')->count();
        $approvedRequests = $locationRequests->where('status', 'approved')->count();
        $rejectedRequests = $locationRequests->where('status', 'rejected')->count();
        $totalLocations = $allLocations->count();
        
        return view('admin.locations.index', compact(
            'locationRequests',
            'allLocations',
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests',
            'totalLocations'
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
            ->where('lr.type', 'municipality');
        
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
            ->where('lr.type', 'barangay');
        
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
    
    public function approve($id)
    {
        Log::info('Approve method called for ID: ' . $id);
        
        try {
            DB::beginTransaction();
            
            $request = DB::table('location_requests')->where('id', $id)->first();
            
            if (!$request) {
                return redirect()->back()->with('error', 'Request not found');
            }
            
            $type = $request->type;
            Log::info('Request type: ' . $type);
            
            if ($type === 'municipality') {
                Log::info('Processing municipality request');
                Log::info('Region field value: ' . ($request->region ?? 'NULL'));
                
                // Check if region is available
                if (!$request->region) {
                    Log::info('Region is missing, returning error');
                    return redirect()->back()->with('error', 'Cannot approve municipality request: Region information is missing. Please ask the staff to resubmit the request with region information.');
                }
                
                // Create actual municipality record
                $municipalityId = DB::table('municipalities')->insertGetId([
                    'name' => $request->name,
                    'province' => $request->region,
                    'status' => 'approved',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Update request status
                DB::table('location_requests')
                    ->where('id', $id)
                    ->update([
                        'status' => 'approved',
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
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                        'updated_at' => now(),
                    ]);
                
                $message = 'Barangay request approved successfully.';
            } else {
                Log::error('Unknown request type: ' . $type);
                DB::rollBack();
                return redirect()->back()->with('error', 'Unknown request type: ' . $type);
            }
            
            Log::info('About to commit transaction with message: ' . $message);
            DB::commit();
            Log::info('Transaction committed, redirecting with success');
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approval error: ' . $e->getMessage());
            Log::error('Approval error trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Failed to approve request: ' . $e->getMessage());
        }
    }
    
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);
        
        try {
            DB::beginTransaction();
            
            $locationRequest = DB::table('location_requests')->where('id', $id)->first();
            
            if (!$locationRequest) {
                return redirect()->back()->with('error', 'Request not found');
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
            
            $message = 'Location request rejected successfully.';
            
            DB::commit();
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Rejection error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reject request');
        }
    }
    
    public function restore($id)
    {
        try {
            DB::beginTransaction();
            
            $locationRequest = DB::table('location_requests')->where('id', $id)->first();
            
            if (!$locationRequest) {
                return redirect()->back()->with('error', 'Request not found');
            }
            
            // Update request status back to pending
            DB::table('location_requests')
                ->where('id', $id)
                ->update([
                    'status' => 'pending',
                    'rejection_reason' => null,
                    'approved_by' => null,
                    'approved_at' => null,
                    'updated_at' => now(),
                ]);
            
            DB::commit();
            return redirect()->back()->with('success', 'Request restored successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Restore error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to restore request');
        }
    }
    
    public function show($id)
    {
        $locationRequest = DB::table('location_requests')
            ->leftJoin('users as requested_by', 'location_requests.requested_by', '=', 'requested_by.id')
            ->leftJoin('users as approved_by', 'location_requests.approved_by', '=', 'approved_by.id')
            ->select(
                'location_requests.*',
                'requested_by.first_name as requested_by_firstname',
                'requested_by.last_name as requested_by_lastname',
                'approved_by.first_name as approved_by_firstname',
                'approved_by.last_name as approved_by_lastname'
            )
            ->where('location_requests.id', $id)
            ->first();
            
        if (!$locationRequest) {
            return redirect()->route('admin.locations.index')->with('error', 'Request not found');
        }
        
        return view('admin.locations.show', compact('locationRequest'));
    }
    
    public function create()
    {
        $municipalities = Municipality::where('status', 'approved')
            ->orderBy('name')
            ->get();

        return view('admin.locations.create', compact('municipalities'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'request_type' => 'required|in:municipality,barangay',
            'region' => 'required_if:request_type,municipality|string|max:255',
            'municipality_id' => 'required_if:request_type,barangay|exists:municipalities,id',
        ]);
        
        try {
            DB::table('location_requests')->insert([
                'name' => $validated['name'],
                'type' => $validated['request_type'],
                'region' => $validated['region'] ?? null,
                'municipality_id' => $validated['municipality_id'] ?? null,
                'requested_by' => Auth::id(),
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Store error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to add location');
        }
    }
    
    public function edit($id)
    {
        $locationRequest = DB::table('location_requests')
            ->where('id', $id)
            ->where('status', 'approved')
            ->first();
            
        if (!$locationRequest) {
            return redirect()->route('admin.locations.index')->with('error', 'Location not found');
        }
        
        return view('admin.locations.edit', compact('locationRequest'));
    }
    
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'region' => 'required_if:request_type,municipality|string|max:255',
            'municipality_id' => 'required_if:request_type,barangay|exists:municipalities,id',
        ]);
        
        try {
            $locationRequest = DB::table('location_requests')->where('id', $id)->first();
            
            if (!$locationRequest) {
                return redirect()->back()->with('error', 'Location not found');
            }
            
            // Update request
            DB::table('location_requests')
                ->where('id', $id)
                ->update([
                    'name' => $validated['name'],
                    'region' => $validated['region'] ?? null,
                    'municipality_id' => $validated['municipality_id'] ?? null,
                    'updated_at' => now(),
                ]);
            
            // Update actual location record
            if ($locationRequest->type === 'municipality') {
                DB::table('municipalities')
                    ->where('name', $locationRequest->name)
                    ->update([
                        'name' => $validated['name'],
                        'province' => $validated['region'],
                        'updated_at' => now(),
                    ]);
            } elseif ($locationRequest->type === 'barangay') {
                DB::table('barangays')
                    ->where('name', $locationRequest->name)
                    ->update([
                        'name' => $validated['name'],
                        'municipality_id' => $validated['municipality_id'],
                        'updated_at' => now(),
                    ]);
            }
            
            return redirect()->route('admin.locations.index')->with('success', 'Location updated successfully!');
            
        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update location');
        }
    }
    
    public function destroy($id)
    {
        try {
            $locationRequest = DB::table('location_requests')->where('id', $id)->first();
            
            if (!$locationRequest) {
                return redirect()->back()->with('error', 'Location not found');
            }
            
            // Delete actual location record if approved
            if ($locationRequest->status === 'approved') {
                if ($locationRequest->type === 'municipality') {
                    DB::table('municipalities')
                        ->where('name', $locationRequest->name)
                        ->delete();
                } elseif ($locationRequest->type === 'barangay') {
                    DB::table('barangays')
                        ->where('name', $locationRequest->name)
                        ->delete();
                }
            }
            
            // Delete request
            DB::table('location_requests')->where('id', $id)->delete();
            
            return redirect()->back()->with('success', 'Location deleted successfully!');
            
        } catch (\Exception $e) {
            Log::error('Destroy error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete location');
        }
    }
    
    public function viewDetails($type, $id)
    {
        $request = DB::table('location_requests as lr')
            ->join('users as u', 'u.id', '=', 'lr.requested_by')
            ->leftJoin('users as ru', 'ru.id', '=', 'lr.reviewed_by')
            ->leftJoin('municipalities as m', 'm.id', '=', 'lr.municipality_id')
            ->where('lr.id', $id)
            ->where('lr.type', $type)
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
