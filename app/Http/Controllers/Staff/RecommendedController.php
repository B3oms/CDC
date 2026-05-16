<?php

namespace App\Http\Controllers\Staff;

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
        $barangays   = Barangay::all();
        $barangayId  = $request->barangay_id;

        $recommended = RecommendedBeneficiary::with(['barangay', 'submittedBy'])
            ->when($barangayId, fn($q) => $q->where('barangay_id', $barangayId))
            ->latest()->paginate(20);

        return view('staff.recommended.index', compact('recommended', 'barangays', 'barangayId'));
    }

    // Convert recommended to full beneficiary interview
    public function convert($id)
    {
        $recommended = RecommendedBeneficiary::findOrFail($id);
        $municipalities = Municipality::with('barangays')->get();
        $barangays      = Barangay::all();

        if ($recommended->status === 'Pending') {
            NotificationService::barangayRecommendationViewed($recommended->id);
        }

        return view('staff.beneficiaries.create', compact(
            'municipalities',
            'barangays'
        ))->with('prefill', $recommended);
    }

    public function reject($id)
    {
        $recommended = RecommendedBeneficiary::findOrFail($id);
        $recommended->update(['status' => 'Rejected']);

        NotificationService::barangayRecommendationRejected($recommended->id);

        return back()->with('success', 'Recommendation rejected.');
    }
}