<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Municipality;
use App\Models\Beneficiary;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BeneficiaryController extends Controller
{
    public function index(Request $request)
{
    $municipalities = Municipality::all();

    $barangays = Barangay::when($request->municipality_id, function ($q) use ($request) {
        $q->where('municipality_id', $request->municipality_id);
    })->get();

    $beneficiaries = Beneficiary::with('barangay.municipality')
        ->when($request->municipality_id, function ($q) use ($request) {
            $q->whereHas('barangay', function ($q2) use ($request) {
                $q2->where('municipality_id', $request->municipality_id);
            });
        })
        ->when($request->barangay_id, function ($q) use ($request) {
            $q->where('barangay_id', $request->barangay_id);
        })
        ->when($request->status, function ($q) use ($request) {
            $q->where('is_verified', $request->status == 'verified');
        })
        ->paginate(10);

    return view('admin.beneficiaries.index', compact(
        'beneficiaries',
        'municipalities',
        'barangays'
    ));
}

    public function show($id)
    {
        $beneficiary = Beneficiary::with([
            'barangay',
            'interviewer',
            'distributions.reliefOperation.calamity',
        ])->findOrFail($id);

        return view('admin.beneficiaries.show', compact('beneficiary'));
    }

    public function downloadPDF(Request $request)
{
    $beneficiaries = Beneficiary::with('barangay')
        ->when($request->municipality_id, function ($q) use ($request) {
            $q->whereHas('barangay', function ($q2) use ($request) {
                $q2->where('municipality_id', $request->municipality_id);
            });
        })
        ->when($request->barangay_id, function ($q) use ($request) {
            $q->where('barangay_id', $request->barangay_id);
        })
        ->when($request->status, function ($q) use ($request) {
            $q->where('is_verified', $request->status == 'verified');
        })
        ->get();

    $pdf = Pdf::loadView('admin.beneficiaries.pdf', compact('beneficiaries'));

    return $pdf->download('beneficiaries.pdf');
}
}

    