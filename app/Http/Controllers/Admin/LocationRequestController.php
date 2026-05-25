<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocationRequest;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LocationRequestController extends Controller
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
        $municipalities = Municipality::orderBy('name')->get();
        $barangays = Barangay::orderBy('name')->get();
        $allLocations = $municipalities->concat($barangays);
        
        // Get orphaned barangays (barangays without valid municipality)
        $orphanedBarangays = $barangays->filter(function ($barangay) use ($municipalities) {
            return !$municipalities->contains('id', $barangay->municipality_id);
        });

        // Get statistics
        $pendingRequests = $locationRequests->where('status', 'pending')->count();
        $approvedRequests = $locationRequests->where('status', 'approved')->count();
        $rejectedRequests = $locationRequests->where('status', 'rejected')->count();
        $totalLocations = $allLocations->count();
        
        return view('admin.locations.index', compact(
            'locationRequests',
            'allLocations',
            'municipalities',
            'barangays',
            'orphanedBarangays',
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests',
            'totalLocations'
        ));
    }


    public function show($id)
    {
        try {
            $locationRequest = LocationRequest::with(['requester', 'municipality', 'approver'])->findOrFail($id);
            
            return view('admin.locations.show', compact('locationRequest'));
        } catch (\Exception $e) {
            \Log::error('Location request show failed: ' . $e->getMessage());
            return redirect()->route('admin.locations.index')->with('error', 'Location request not found.');
        }
    }

    public function approve($id)
    {
        try {
            $locationRequest = LocationRequest::findOrFail($id);
            
            // Debug: Log the current status
            \Log::info('Attempting to approve LocationRequest ID: ' . $locationRequest->id . ', Current status: ' . $locationRequest->status);
            
            if (!$locationRequest->canBeApproved()) {
                return back()->with('error', 'This request cannot be approved. Current status: ' . $locationRequest->status);
            }

            DB::beginTransaction();

            // Update request status
            $locationRequest->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Create the actual location record
            if ($locationRequest->type === 'municipality') {
                Municipality::create([
                    'name' => $locationRequest->name,
                    'province' => $locationRequest->region ?? 'Unknown', // Use region as province or default
                    'status' => 'approved',
                    'notes' => $locationRequest->remarks,
                ]);
            } elseif ($locationRequest->type === 'barangay') {
                Barangay::create([
                    'name' => $locationRequest->name,
                    'municipality_id' => $locationRequest->municipality_id,
                ]);
            }

            // Trigger notification for approval (optional - don't fail if notification fails)
            try {
                if (class_exists('App\Services\NotificationService')) {
                    NotificationService::locationRequestApproved($locationRequest->id, Auth::id());
                }
            } catch (\Exception $notificationException) {
                \Log::warning('Notification failed but approval succeeded: ' . $notificationException->getMessage());
            }

            DB::commit();

            return back()->with('success', 'Location request approved successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Location request approval failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Failed to approve location request. Error: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $locationRequest = LocationRequest::findOrFail($id);

            DB::beginTransaction();

            // Update request status
            $locationRequest->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'rejection_reason' => $request->input('rejection_reason'),
            ]);

            // Trigger notification for rejection (optional - don't fail if notification fails)
            try {
                NotificationService::locationRequestRejected($locationRequest->id, Auth::id(), $request->input('rejection_reason'));
            } catch (\Exception $notificationException) {
                \Log::warning('Notification failed but rejection succeeded: ' . $notificationException->getMessage());
            }

            DB::commit();

            return back()->with('success', 'Location request rejected successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Location request rejection failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject location request. Error: ' . $e->getMessage());
        }
    }

    public function edit(LocationRequest $locationRequest)
    {
        if (!$locationRequest->canBeApproved()) {
            abort(403, 'This request cannot be edited.');
        }

        $locationRequest->load(['requester', 'municipality']);
        $municipalities = Municipality::where('status', 'approved')
            ->orderBy('name')
            ->get();

        return view('admin.location-requests.edit', compact('locationRequest', 'municipalities'));
    }

    public function update(Request $request, LocationRequest $locationRequest)
    {
        if (!$locationRequest->canBeApproved()) {
            abort(403, 'This request cannot be updated.');
        }

        $validated = $request->validate([
            'type' => 'required|in:municipality,barangay',
            'municipality_id' => 'required_if:type,barangay|exists:municipalities,id',
            'name' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        try {
            $locationRequest->update($validated);

            return redirect()
                ->route('admin.location-requests.index')
                ->with('success', 'Location request updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update location request. Please try again.');
        }
    }

    public function destroy(LocationRequest $locationRequest)
    {
        try {
            $locationRequest->delete();

            return back()->with('success', 'Location request deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete location request. Please try again.');
        }
    }
}
