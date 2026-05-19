<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HouseholdController extends Controller
{
    // Display all households
    public function index()
    {
        return view('staff.households.index');
    }

    // Show household creation form
    public function create()
    {
        return view('staff.households.create');
    }

    // Store new household
    public function store(Request $request)
    {
        // Placeholder for household creation logic
        return redirect()->route('staff.households.index')
            ->with('success', 'Household created successfully.');
    }

    // Show specific household
    public function show($id)
    {
        return view('staff.households.show', compact('id'));
    }

    // Show household edit form
    public function edit($id)
    {
        return view('staff.households.edit', compact('id'));
    }

    // Update household
    public function update(Request $request, $id)
    {
        // Placeholder for household update logic
        return redirect()->route('staff.households.show', $id)
            ->with('success', 'Household updated successfully.');
    }

    // Delete household
    public function destroy($id)
    {
        // Placeholder for household deletion logic
        return redirect()->route('staff.households.index')
            ->with('success', 'Household deleted successfully.');
    }
}
