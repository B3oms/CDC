<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecommendedBeneficiary;
use App\Models\Barangay;
use App\Models\Municipality;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class RecommendedController extends Controller
{
    public function index(Request $request)
    {
        $barangays = Barangay::all();
        $municipalities = Municipality::all();
        
        $query = RecommendedBeneficiary::with(['barangay.municipality']);
        
        if ($request->barangay_id) {
            $query->where('barangay_id', $request->barangay_id);
        }
        
        if ($request->municipality_id) {
            $query->whereHas('barangay', function($q) use ($request) {
                $q->where('municipality_id', $request->municipality_id);
            });
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $recommended = $query->latest()->paginate(10);
        
        return view('admin.recommended.index', compact('recommended', 'barangays', 'municipalities'));
    }

    public function show($id)
    {
        $recommended = RecommendedBeneficiary::with(['barangay.municipality'])->findOrFail($id);
        return view('admin.recommended.show', compact('recommended'));
    }

    public function approve($id)
    {
        $recommended = RecommendedBeneficiary::findOrFail($id);
        $recommended->update(['status' => 'approved']);
        
        return back()->with('success', 'Recommendation approved successfully.');
    }

    public function reject($id)
    {
        $recommended = RecommendedBeneficiary::findOrFail($id);
        $recommended->update(['status' => 'rejected']);
        NotificationService::recommendationRejected($recommended->id);

        return back()->with('success', 'Recommendation rejected successfully.');
    }

    public function convert($id)
    {
        $recommended = RecommendedBeneficiary::findOrFail($id);
        
        // Convert to beneficiary
        $beneficiary = \App\Models\Beneficiary::create([
            'first_name' => $recommended->first_name,
            'middle_name' => $recommended->middle_name,
            'last_name' => $recommended->last_name,
            'gender' => $recommended->gender,
            'age' => $recommended->age,
            'contact_number' => $recommended->contact_number,
            'address' => $recommended->address,
            'barangay_id' => $recommended->barangay_id,
            'is_verified' => true,
            'interviewed_by' => auth()->id(),
            'interview_date' => now(),
        ]);
        
        // Update recommendation status
        $recommended->update(['status' => 'converted']);
        NotificationService::recommendationConverted($recommended->id);

        return redirect()->route('admin.beneficiaries.show', $beneficiary->id)
            ->with('success', 'Recommendation converted to beneficiary successfully.');
    }

    public function destroy($id)
    {
        $recommended = RecommendedBeneficiary::findOrFail($id);
        $recommended->delete();
        
        return redirect()->route('recommended.index')
            ->with('success', 'Recommendation deleted successfully.');
    }
}
