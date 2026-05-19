<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HouseholdRequest;
use App\Models\HouseholdMember;
use Illuminate\Http\Request;

class HouseholdRequestController extends Controller
{
    // Display all household requests for admin
    public function index()
    {
        $requests = HouseholdRequest::with(['barangay', 'members', 'approvedBy'])
            ->latest()
            ->get();

        // Calculate statistics
        $statistics = [
            'total' => HouseholdRequest::count(),
            'pending' => HouseholdRequest::where('status', 'pending')->count(),
            'approved' => HouseholdRequest::where('status', 'approved')->count(),
            'rejected' => HouseholdRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.household_requests.index', compact('requests', 'statistics'));
    }

    // Show specific household request
    public function show($id)
    {
        $request = HouseholdRequest::with(['barangay', 'members', 'approvedBy'])
            ->findOrFail($id);

        return view('admin.household_requests.show', compact('request'));
    }

    // Approve household request
    public function approve(Request $request, $id)
    {
        $validated = $request->validate([
            'staff_notes' => 'nullable|string',
        ]);

        $householdRequest = HouseholdRequest::findOrFail($id);
        
        $householdRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $validated['staff_notes'] ?? null,
        ]);

        return redirect()->route('admin.household_requests.show', $id)
            ->with('success', 'Household request approved successfully.');
    }

    // Reject household request
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
            'staff_notes' => 'nullable|string',
        ]);

        $householdRequest = HouseholdRequest::findOrFail($id);
        
        $householdRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
            'notes' => $validated['staff_notes'] ?? null,
        ]);

        return redirect()->route('admin.household_requests.show', $id)
            ->with('success', 'Household request rejected.');
    }

    // Delete household request
    public function destroy($id)
    {
        $householdRequest = HouseholdRequest::findOrFail($id);
        $householdRequest->delete();

        return redirect()->route('admin.household_requests.index')
            ->with('success', 'Household request deleted successfully.');
    }
}
