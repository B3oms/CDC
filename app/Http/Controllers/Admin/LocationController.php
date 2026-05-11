<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\MunicipalityRequest;
use App\Models\BarangayRequest;

class LocationController extends Controller
{
    public function index()
    {
        // Get municipality requests
        $municipalityRequests = MunicipalityRequest::with(['requester'])
            ->latest()
            ->get();
        
        // Get barangay requests grouped by municipality
        $barangayRequests = BarangayRequest::with(['requester', 'municipality'])
            ->latest()
            ->get()
            ->groupBy('municipality_id');

        return view('admin.locations.index', compact('municipalityRequests', 'barangayRequests'));
    }

    public function approve($id)
    {
        try {
            DB::beginTransaction();

            // Try to find in municipality requests first
            $municipalityRequest = MunicipalityRequest::find($id);
            
            if ($municipalityRequest) {
                // Create actual municipality record
                $municipality = Municipality::create([
                    'name' => $municipalityRequest->name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update request status
                $municipalityRequest->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);

                DB::commit();
                return back()->with('success', 'Municipality request approved successfully.');
            }

            // Try barangay request
            $barangayRequest = BarangayRequest::find($id);
            
            if ($barangayRequest) {
                // Create actual barangay record
                $barangay = Barangay::create([
                    'name' => $barangayRequest->name,
                    'municipality_id' => $barangayRequest->municipality_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update request status
                $barangayRequest->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);

                DB::commit();
                return back()->with('success', 'Barangay request approved successfully.');
            }

            throw new \Exception('Request not found.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approval error: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve request.');
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Validate rejection reason
            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:255',
            ]);

            // Try to find in municipality requests first
            $municipalityRequest = MunicipalityRequest::find($id);
            
            if ($municipalityRequest) {
                // Update request status
                $municipalityRequest->update([
                    'status' => 'rejected',
                    'rejection_reason' => $validated['rejection_reason'],
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);

                DB::commit();
                return back()->with('success', 'Municipality request rejected successfully.');
            }

            // Try barangay request
            $barangayRequest = BarangayRequest::find($id);
            
            if ($barangayRequest) {
                // Update request status
                $barangayRequest->update([
                    'status' => 'rejected',
                    'rejection_reason' => $validated['rejection_reason'],
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);

                DB::commit();
                return back()->with('success', 'Barangay request rejected successfully.');
            }

            throw new \Exception('Request not found.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Rejection error: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject request.');
        }
    }

    public function destroy($id)
    {
        try {
            // Try to find in municipality requests first
            $municipalityRequest = MunicipalityRequest::find($id);
            
            if ($municipalityRequest) {
                $municipalityRequest->delete();
                return back()->with('success', 'Municipality request deleted successfully.');
            }

            // Try barangay request
            $barangayRequest = BarangayRequest::find($id);
            
            if ($barangayRequest) {
                $barangayRequest->delete();
                return back()->with('success', 'Barangay request deleted successfully.');
            }

            throw new \Exception('Request not found.');

        } catch (\Exception $e) {
            Log::error('Deletion error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete request.');
        }
    }
}
