<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    public function index(Request $request)
    {
        // Get beneficiaries for the logged-in user's barangay only, excluding rejected ones
        $query = Beneficiary::where('barangay_id', auth()->user()->barangay_id)
            ->where('status', '!=', 'rejected') // Exclude rejected beneficiaries
            ->with('barangay');

        // Filter by gender
        if ($request->gender) {
            $query->where('gender', $request->gender);
        }

        // Filter by 4Ps membership
        if ($request->is_4ps_member !== null) {
            $query->where('is_4ps_member', $request->is_4ps_member);
        }

        // Filter by vulnerability level (only for verified beneficiaries)
        if ($request->vulnerability_level) {
            $query->where('vulnerability_level', $request->vulnerability_level);
        }

        $beneficiaries = $query->latest()->paginate(20);

        // Get statistics for the barangay (excluding rejected beneficiaries)
        $stats = [
            'total' => Beneficiary::where('barangay_id', auth()->user()->barangay_id)->where('status', '!=', 'rejected')->count(),
            'verified' => Beneficiary::where('barangay_id', auth()->user()->barangay_id)->where('status', 'verified')->count(),
            'pending' => Beneficiary::where('barangay_id', auth()->user()->barangay_id)->where('status', 'pending')->count(),
            'high_vulnerability' => Beneficiary::where('barangay_id', auth()->user()->barangay_id)->where('vulnerability_level', 'High')->where('status', 'verified')->count(),
            'medium_vulnerability' => Beneficiary::where('barangay_id', auth()->user()->barangay_id)->where('vulnerability_level', 'Medium')->where('status', 'verified')->count(),
            'low_vulnerability' => Beneficiary::where('barangay_id', auth()->user()->barangay_id)->where('vulnerability_level', 'Low')->where('status', 'verified')->count(),
        ];

        return view('barangay.beneficiaries.index', compact('beneficiaries', 'stats'));
    }

    public function show($id)
    {
        // Only allow viewing beneficiaries from the same barangay, excluding rejected ones
        $beneficiary = Beneficiary::where('barangay_id', auth()->user()->barangay_id)
            ->where('status', '!=', 'rejected') // Exclude rejected beneficiaries
            ->with([
                'barangay', 
                'interviewer',
                'distributions.reliefOperation.calamity'
            ])
            ->findOrFail($id);

        return view('barangay.beneficiaries.show', compact('beneficiary'));
    }
}
