<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use App\Models\HouseholdRequest;
use App\Models\HouseholdMember;
use Illuminate\Http\Request;

class HouseholdRequestController extends Controller
{
    // Display all household requests for the barangay
    public function index()
    {
        $requests = HouseholdRequest::where('barangay_id', auth()->user()->barangay_id)
            ->with('members')
            ->latest()
            ->get();

        // Get households (approved requests) for this barangay
        $households = HouseholdRequest::where('barangay_id', auth()->user()->barangay_id)
            ->where('status', 'approved')
            ->with('members')
            ->orderBy('head_of_household')
            ->get();

        return view('barangay.household_requests.index', compact('requests', 'households'));
    }

    // Show household request creation form
    public function create()
    {
        return view('barangay.household_requests.create');
    }

    // Store new household request
    public function store(Request $request)
    {
        $validated = $request->validate([
            'head_name' => 'required|string|max:255',
            'head_age' => 'required|integer|min:18|max:110',
            'head_sex' => 'required|in:male,female',
            'head_date_of_birth' => 'required|date|before:today',
            'address' => 'required|string',
            'contact_number' => 'required|string|max:20',
            'members' => 'required|array|min:0',
            'members.*.name' => 'required|string|max:255',
            'members.*.age' => 'required|integer|min:18|max:110',
            'members.*.sex' => 'required|in:male,female',
        ]);

        // Create household request
        $householdRequest = HouseholdRequest::create([
            'barangay_id' => auth()->user()->barangay_id,
            'head_of_household' => $validated['head_name'],
            'head_age' => $validated['head_age'],
            'head_sex' => $validated['head_sex'],
            'birthday' => $validated['head_date_of_birth'],
            'address' => $validated['address'],
            'contact_number' => $validated['contact_number'],
            'family_size' => 1 + count($validated['members']), // Head + family members
            'requested_by' => auth()->id(),
        ]);

        // Create household members
        if (!empty($validated['members'])) {
            foreach ($validated['members'] as $member) {
                HouseholdMember::create([
                    'household_request_id' => $householdRequest->id,
                    'name' => $member['name'],
                    'age' => $member['age'],
                    'sex' => $member['sex'],
                ]);
            }
        }

        return redirect()->route('barangay.household_requests.index')
            ->with('success', 'Household request submitted successfully. It will be reviewed by staff.');
    }

    // Show specific household request
    public function show($id)
    {
        $request = HouseholdRequest::where('barangay_id', auth()->user()->barangay_id)
            ->with('members', 'approvedBy')
            ->findOrFail($id);

        return view('barangay.household_requests.show', compact('request'));
    }

    // Show edit form for household request
    public function edit($id)
    {
        $request = HouseholdRequest::where('barangay_id', auth()->user()->barangay_id)
            ->where('status', 'pending') // Only allow editing pending requests
            ->with('members')
            ->findOrFail($id);

        return view('barangay.household_requests.edit', compact('request'));
    }

    // Update household request
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'head_name' => 'required|string|max:255',
            'head_age' => 'required|integer|min:18|max:110',
            'head_sex' => 'required|in:male,female',
            'head_date_of_birth' => 'required|date|before:today',
            'address' => 'required|string',
            'contact_number' => 'required|string|max:20',
            'members' => 'required|array|min:0',
            'members.*.name' => 'required|string|max:255',
            'members.*.age' => 'required|integer|min:18|max:110',
            'members.*.sex' => 'required|in:male,female',
        ]);

        $householdRequest = HouseholdRequest::where('barangay_id', auth()->user()->barangay_id)
            ->where('status', 'approved') // Only allow editing approved households
            ->findOrFail($id);

        // Update household request
        $householdRequest->update([
            'head_of_household' => $validated['head_name'],
            'head_age' => $validated['head_age'],
            'head_sex' => $validated['head_sex'],
            'birthday' => $validated['head_date_of_birth'],
            'address' => $validated['address'],
            'contact_number' => $validated['contact_number'],
            'family_size' => 1 + count($validated['members']),
        ]);

        // Delete existing members and recreate them
        $householdRequest->members()->delete();
        
        // Create household members
        if (!empty($validated['members'])) {
            foreach ($validated['members'] as $member) {
                HouseholdMember::create([
                    'household_request_id' => $householdRequest->id,
                    'name' => $member['name'],
                    'age' => $member['age'],
                    'sex' => $member['sex'],
                ]);
            }
        }

        return redirect()->route('barangay.household_requests.show', $id)
            ->with('success', 'Household request updated successfully.');
    }

    // Display all households (approved requests) in the barangay
    public function households(Request $request)
    {
        $query = HouseholdRequest::where('barangay_id', auth()->user()->barangay_id)
            ->where('status', 'approved') // Only show approved households
            ->with('members', 'approvedBy');

        // Filter by family size
        if ($request->family_size) {
            $query->where('family_size', $request->family_size);
        }

        $households = $query->latest('approved_at')->paginate(20);

        return view('barangay.household_requests.households', compact('households'));
    }
}
