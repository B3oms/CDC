<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Municipality;
use App\Models\Beneficiary;
use App\Models\Barangay;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    public function index(Request $request)
{
    $municipalities = Municipality::all();

    $barangays = Barangay::when($request->municipality_id, function ($q) use ($request) {
        $q->where('municipality_id', $request->municipality_id);
    })->get();

    $beneficiaries = Beneficiary::with(['barangay.municipality', 'user'])
        ->when($request->municipality_id, function ($q) use ($request) {
            $q->whereHas('barangay', function ($q2) use ($request) {
                $q2->where('municipality_id', $request->municipality_id);
            });
        })
        ->when($request->barangay_id, function ($q) use ($request) {
            $q->where('barangay_id', $request->barangay_id);
        })
        ->when($request->gender, function ($q) use ($request) {
            $q->where('gender', $request->gender);
        })
        ->when($request->is_4ps_member !== null, function ($q) use ($request) {
            $q->where('is_4ps_member', $request->is_4ps_member);
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
        try {
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

            // Get paper size and orientation from request (default to A4 portrait)
            $paperSize = $request->input('paper_size', 'A4');
            $orientation = $request->input('orientation', 'portrait');

            // Generate PDF using DomPDF
            $html = view('admin.beneficiaries.pdf', compact('beneficiaries'))->render();
            
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            
            // Set paper size and orientation
            $dompdf->setPaper($paperSize, $orientation);
            
            // Set options for better rendering
            $options = new \Dompdf\Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isFontSubsettingEnabled', true);
            $dompdf->setOptions($options);
            
            // Render the PDF
            $dompdf->render();
            
            // Return PDF download
            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="beneficiaries.pdf"'
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('PDF generation failed: ' . $e->getMessage());
            
            // Return a simple error response
            return response()->json([
                'error' => 'PDF generation failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function edit($id)
    {
        $beneficiary = Beneficiary::findOrFail($id);
        $municipalities = Municipality::with('barangays')->get();
        $barangays = Barangay::all();
        
        return view('admin.beneficiaries.edit', compact('beneficiary', 'municipalities', 'barangays'));
    }

    public function update(Request $request, $id)
    {
        $beneficiary = Beneficiary::findOrFail($id);
        
        $request->validate([
            'municipality_id'  => 'required|exists:municipalities,id',
            'barangay_id'     => 'required|exists:barangays,id',
            'first_name'      => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'middle_name'     => 'nullable|string|max:100|regex:/^[a-zA-Z\s]*$/',
            'last_name'       => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'suffix'          => 'nullable|string|max:20',
            'gender'          => 'required|in:Male,Female,Other',
            'is_4ps_member'   => 'required|boolean',
            'birthdate'       => 'required|date',
            'contact_number'  => 'nullable|string|regex:/^[0-9]{11}$/',
            'address'         => 'nullable|string',
            'family_size'     => 'required|integer|min:1',
            'monthly_income'  => 'required|numeric|min:0',
            'children_count'  => 'required|integer|min:0',
            'has_senior'      => 'required|boolean',
            'interview_notes'  => 'nullable|string',
        ], [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'birthdate.required' => 'Birthdate is required.',
        ]);

        // Auto-verification
        $criteriaMet = Beneficiary::checkCriteria(
            $request->family_size,
            $request->monthly_income,
            $request->has_senior ?? false,
            $request->children_count,
            $request->is_4ps_member ?? false
        );

        $isVerified       = $criteriaMet >= 3 ? 1 : 0;
        $vulnerabilityLevel = match(true) {
            $criteriaMet >= 4 => 'High',
            $criteriaMet == 3 => 'Medium',
            default           => 'Low',
        };

        $beneficiary->update([
            'barangay_id'        => $request->barangay_id,
            'first_name'         => $request->first_name,
            'middle_name'        => $request->middle_name,
            'last_name'          => $request->last_name,
            'suffix'             => $request->suffix,
            'gender'             => $request->gender,
            'is_4ps_member'      => $request->is_4ps_member,
            'birthdate'          => $request->birthdate,
            'contact_number'     => $request->contact_number,
            'address'            => $request->address,
            'family_size'        => $request->family_size,
            'monthly_income'     => $request->monthly_income,
            'vulnerability_level'=> $vulnerabilityLevel,
            'has_senior'         => $request->has_senior ?? false,
            'children_count'     => $request->children_count,
            'criteria_met'       => $criteriaMet,
            'interview_notes'    => $request->interview_notes,
            'is_verified'        => $isVerified,
        ]);

        return redirect()->route('admin.beneficiaries.index')
            ->with('success', 'Beneficiary updated successfully.');
    }

    public function destroy($id)
    {
        $beneficiary = Beneficiary::findOrFail($id);
        
        // Delete associated user account if exists
        if ($beneficiary->user_id) {
            $user = $beneficiary->user;
            if ($user) {
                $user->delete();
            }
        }
        
        // Delete beneficiary
        $beneficiary->delete();
        
        return redirect()->route('admin.beneficiaries.index')
            ->with('success', 'Beneficiary deleted successfully.');
    }
}

    