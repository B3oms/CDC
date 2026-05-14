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
        $requests = LocationRequest::with(['requester', 'municipality'])
            ->latest()
            ->get();

        return view('admin.location-requests.index', compact('requests'));
    }

    public function show(LocationRequest $locationRequest)
    {
        $locationRequest->load(['requester', 'municipality', 'approver']);
        
        return view('admin.location-requests.show', compact('locationRequest'));
    }

    public function approve(LocationRequest $locationRequest)
    {
        if (!$locationRequest->canBeApproved()) {
            return back()->with('error', 'This request cannot be approved.');
        }

        try {
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
                    'status' => 'approved',
                    'notes' => $locationRequest->remarks,
                ]);
            } elseif ($locationRequest->type === 'barangay') {
                Barangay::create([
                    'name' => $locationRequest->name,
                    'municipality_id' => $locationRequest->municipality_id,
                ]);
            }

            // Trigger notification for approval
            NotificationService::locationRequestApproved($locationRequest->id, Auth::id());

            DB::commit();

            return back()->with('success', 'Location request approved successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve location request. Please try again.');
        }
    }

    public function reject(Request $request, LocationRequest $locationRequest)
    {
        if (!$locationRequest->canBeRejected()) {
            return back()->with('error', 'This request cannot be rejected.');
        }

        try {
            DB::beginTransaction();

            // Update request status
            $locationRequest->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'rejection_reason' => $request->input('rejection_reason'),
            ]);

            // Trigger notification for rejection
            NotificationService::locationRequestRejected($locationRequest->id, Auth::id(), $request->input('rejection_reason'));

            DB::commit();

            return back()->with('success', 'Location request rejected successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject location request. Please try again.');
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
