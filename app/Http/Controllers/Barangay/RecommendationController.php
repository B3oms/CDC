<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use App\Models\RecommendedBeneficiary;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function index()
    {
        $recommendations = RecommendedBeneficiary::where('barangay_id', auth()->user()->barangay_id)
            ->latest()->get();

        return view('barangay.recommendations', compact('recommendations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'contact_number' => 'nullable|string|max:13',
            'address'        => 'nullable|string',
        ]);

        RecommendedBeneficiary::create([
            'barangay_id'    => auth()->user()->barangay_id,
            'submitted_by'   => auth()->id(),
            'first_name'     => $request->first_name,
            'last_name'      => $request->last_name,
            'contact_number' => $request->contact_number,
            'address'        => $request->address,
            'status'         => 'Pending',
        ]);

        return back()->with('success', 'Recommendation submitted successfully.');
    }
}