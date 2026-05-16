<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\LocationRequest;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LocationRequestController extends Controller
{
    public function index()
    {
        $requests = LocationRequest::with(['requester', 'municipality'])
            ->where('requested_by', Auth::id())
            ->latest()
            ->get();

        return view('staff.locations.index', compact('requests'));
    }

    public function create()
    {
        $municipalities = Municipality::where('status', 'approved')
            ->orderBy('name')
            ->get();

        return view('staff.locations.create', compact('municipalities'));
    }

    public function store(Request $request)
    {
        Log::info('Location request submission data: ', $request->all());
        
        $validated = $request->validate([
            'type' => 'required|in:municipality,barangay',
            'region' => 'required_if:type,municipality|string|max:255',
            'municipality_id' => 'required_if:type,barangay|exists:municipalities,id',
            'name' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        Log::info('Validated data: ', $validated);

        try {
            $locationRequest = LocationRequest::create([
                'requested_by' => Auth::id(),
                'type' => $validated['type'],
                'region' => $validated['region'] ?? null,
                'municipality_id' => $validated['municipality_id'] ?? null,
                'name' => $validated['name'],
                'remarks' => $validated['remarks'] ?? null,
                'status' => 'pending',
            ]);

            NotificationService::locationRequestSubmitted($locationRequest->id);

            return redirect()
                ->route('staff.locations.index')
                ->with('success', 'Location request submitted successfully and is now pending admin approval.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to submit location request. Please try again.');
        }
    }

    public function edit(LocationRequest $locationRequest)
    {
        // Check if user can edit this request
        if (!$locationRequest->canBeEdited()) {
            abort(403, 'You can only edit your own pending requests.');
        }

        $municipalities = Municipality::where('status', 'approved')
            ->orderBy('name')
            ->get();

        return view('staff.locations.edit', compact('locationRequest', 'municipalities'));
    }

    public function update(Request $request, LocationRequest $locationRequest)
    {
        // Check if user can edit this request
        if (!$locationRequest->canBeEdited()) {
            abort(403, 'You can only edit your own pending requests.');
        }

        $validated = $request->validate([
            'type' => 'required|in:municipality,barangay',
            'municipality_id' => 'required_if:type,barangay|exists:municipalities,id',
            'name' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        try {
            $locationRequest->update([
                'type' => $validated['type'],
                'municipality_id' => $validated['municipality_id'] ?? null,
                'name' => $validated['name'],
                'remarks' => $validated['remarks'] ?? null,
            ]);

            NotificationService::locationRequestUpdated($locationRequest->id, Auth::id());

            return redirect()
                ->route('staff.locations.index')
                ->with('success', 'Location request updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update location request. Please try again.');
        }
    }

    public function destroy(LocationRequest $locationRequest)
    {
        // Check if user can delete this request
        if (!$locationRequest->canBeEdited()) {
            abort(403, 'You can only delete your own pending requests.');
        }

        try {
            $locationRequest->delete();

            return redirect()
                ->route('staff.locations.index')
                ->with('success', 'Location request deleted successfully.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete location request. Please try again.');
        }
    }
}
