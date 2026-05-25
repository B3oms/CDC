<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\RecommendedBeneficiary;
use App\Models\Beneficiary;
use App\Models\Barangay;
use App\Models\Municipality;
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
            ->when(!$status || $status === 'Rejected', function($query) {
                // For rejected beneficiaries, only show those rejected within the last 10 days
                $query->where(function($q) {
                    $q->where('status', '!=', 'Rejected')
                      ->orWhere(function($subQuery) {
                          $subQuery->where('status', 'Rejected')
                                   ->where('updated_at', '>=', now()->subDays(10));
                      });
                });
            })
            ->latest()->paginate(20);

        return view('staff.recommended.index', compact('recommended', 'barangays', 'barangayId', 'status'));
    }

    // Convert recommended to full beneficiary interview
    public function convert($id)
    {
        $recommended    = RecommendedBeneficiary::findOrFail($id);
        $barangays      = Barangay::all();
        $municipalities = Municipality::with('barangays')->get();

        return view('staff.beneficiaries.create', [
            'barangays'      => $barangays,
            'municipalities' => $municipalities,
            'prefill'        => $recommended,
        ]);
    }

    public function reject($id)
    {
        $recommended = RecommendedBeneficiary::findOrFail($id);
        $recommended->update([
            'status' => 'Rejected',
            'updated_at' => now() // Update the timestamp to track when rejection happened
        ]);

        return back()->with('success', 'Recommendation rejected. Will be removed from list after 10 days.');
    }
}