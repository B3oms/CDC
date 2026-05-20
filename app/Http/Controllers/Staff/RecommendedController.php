<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\RecommendedBeneficiary;
use App\Models\Beneficiary;
use App\Models\Barangay;
use Illuminate\Http\Request;

class RecommendedController extends Controller
{
    public function index(Request $request)
    {
        $barangays   = Barangay::all();
        $barangayId  = $request->barangay_id;
        $status      = $request->status;

        $recommended = RecommendedBeneficiary::with(['barangay', 'submittedBy'])
            ->when($barangayId, fn($q) => $q->where('barangay_id', $barangayId))
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()->paginate(20);

        return view('staff.recommended.index', compact('recommended', 'barangays', 'barangayId', 'status'));
    }

    // Convert recommended to full beneficiary interview
    public function convert($id)
    {
        $recommended = RecommendedBeneficiary::findOrFail($id);
        $barangays   = Barangay::all();

        return view('staff.beneficiaries.create', [
            'barangays'   => $barangays,
            'prefill'     => $recommended,
        ]);
    }

    public function reject($id)
    {
        $recommended = RecommendedBeneficiary::findOrFail($id);
        $recommended->update(['status' => 'Rejected']);

        return back()->with('success', 'Recommendation rejected.');
    }
}