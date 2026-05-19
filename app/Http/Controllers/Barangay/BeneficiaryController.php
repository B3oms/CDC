<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    public function index(Request $request)
    {
        // Get beneficiaries for the logged-in user's barangay only
        $query = Beneficiary::where('barangay_id', auth()->user()->barangay_id)
            ->with('barangay');

        // Filter by verification status
        if ($request->status === 'verified') {
            $query->where('is_verified', 1);
        } elseif ($request->status === 'pending') {
            $query->where('is_verified', 0);
        }

        // Filter by gender
        if ($request->gender) {
            $query->where('gender', $request->gender);
        }

        // Filter by 4Ps membership
        if ($request->is_4ps_member !== null) {
            $query->where('is_4ps_member', $request->is_4ps_member);
        }

        // Filter by vulnerability level
        if ($request->vulnerability_level) {
            $query->where('vulnerability_level', $request->vulnerability_level);
        }

        $beneficiaries = $query->latest()->paginate(20);

        // Get statistics for the barangay
        $stats = [
            'total' => Beneficiary::where('barangay_id', auth()->user()->barangay_id)->count(),
            'verified' => Beneficiary::where('barangay_id', auth()->user()->barangay_id)->where('is_verified', 1)->count(),
            'pending' => Beneficiary::where('barangay_id', auth()->user()->barangay_id)->where('is_verified', 0)->count(),
            'high_vulnerability' => Beneficiary::where('barangay_id', auth()->user()->barangay_id)->where('vulnerability_level', 'High')->where('is_verified', 1)->count(),
            'medium_vulnerability' => Beneficiary::where('barangay_id', auth()->user()->barangay_id)->where('vulnerability_level', 'Medium')->where('is_verified', 1)->count(),
            'low_vulnerability' => Beneficiary::where('barangay_id', auth()->user()->barangay_id)->where('vulnerability_level', 'Low')->where('is_verified', 1)->count(),
        ];

        return view('barangay.beneficiaries.index', compact('beneficiaries', 'stats'));
    }

    public function show($id)
    {
        // Only allow viewing beneficiaries from the same barangay
        $beneficiary = Beneficiary::where('barangay_id', auth()->user()->barangay_id)
            ->with([
                'barangay', 
                'interviewer',
                'distributions.reliefOperation.calamity'
            ])
            ->findOrFail($id);

        return view('barangay.beneficiaries.show', compact('beneficiary'));
    }
}
