<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\Barangay;
use App\Models\Municipality;
use App\Models\User;
use App\Models\Role;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PDF;

class BeneficiaryController extends Controller
{
    public function index(Request $request)
    {
        $municipalities = Municipality::with('barangays')->get();
        $barangays = Barangay::all();
        
        // If municipality is selected, get only barangays from that municipality
        if ($request->municipality_id) {
            $barangays = Barangay::where('municipality_id', $request->municipality_id)->get();
        }
        
        $query = Beneficiary::with('barangay');

        if ($request->municipality_id) {
            $query->whereHas('barangay', function($q) use ($request) {
                $q->where('municipality_id', $request->municipality_id);
            });
        }

        if ($request->barangay_id) {
            $query->where('barangay_id', $request->barangay_id);
        }

        if ($request->gender) {
            $query->where('gender', $request->gender);
        }

        if ($request->is_4ps_member !== null) {
            $query->where('is_4ps_member', $request->is_4ps_member);
        }

        if ($request->status === 'verified') {
            $query->where('is_verified', 1);
        } elseif ($request->status === 'pending') {
            $query->where('is_verified', 0);
        }

        $beneficiaries = $query->latest()->paginate(20);

        return view('staff.beneficiaries.index', compact(
            'beneficiaries', 'municipalities', 'barangays'
        ));
    }

    public function create()
    {
        $municipalities = Municipality::with('barangays')->get();
        $barangays = Barangay::all();
        return view('staff.beneficiaries.create', compact('municipalities', 'barangays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'municipality_id'  => 'required|exists:municipalities,id',
            'barangay_id'     => 'required|exists:barangays,id',
            'region'          => 'required|string',
            'first_name'      => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'middle_name'     => 'nullable|string|max:100|regex:/^[a-zA-Z\s]*$/',
            'last_name'       => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'suffix'          => 'nullable|string|max:20',
            'custom_suffix'   => 'nullable|string|max:20|required_if:suffix,Other',
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
        ]);

        // Check 250 slot limit
        $count = Beneficiary::where('barangay_id', $request->barangay_id)
            ->where('is_verified', 1)->count();

        if ($count >= 250) {
            return back()->withErrors([
                'barangay_id' => 'This barangay has reached the 250 beneficiary limit.'
            ])->withInput();
        }

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

        $beneficiary = Beneficiary::create([
            'barangay_id'        => $request->barangay_id,
            'first_name'         => $request->first_name,
            'middle_name'        => $request->middle_name,
            'last_name'          => $request->last_name,
            'suffix'             => $request->suffix === 'Other' ? $request->custom_suffix : $request->suffix,
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
            'interviewed_by'     => auth()->id(),
            'interviewed_at'     => now(),
            'is_verified'        => $isVerified,
        ]);

        // Create beneficiary account if verified
        if ($isVerified) {
            $this->createBeneficiaryAccount($beneficiary);
        }

        // Trigger notification for beneficiary addition
        NotificationService::beneficiaryAdded($beneficiary->id, auth()->id());

        $message = $isVerified
            ? 'Beneficiary verified and added successfully.'
            : 'Beneficiary recorded but did not meet verification criteria.';

        return redirect()->route('staff.beneficiaries.index')
            ->with('success', $message);
    }

    public function show($id)
    {
        $beneficiary = Beneficiary::with([
            'barangay', 'interviewer',
            'distributions.reliefOperation.calamity'
        ])->findOrFail($id);

        return view('staff.beneficiaries.show', compact('beneficiary'));
    }

    public function pdf(Request $request)
    {
        try {
            $query = Beneficiary::with('barangay');

            if ($request->municipality_id) {
                $query->whereHas('barangay', function($q) use ($request) {
                    $q->where('municipality_id', $request->municipality_id);
                });
            }

            if ($request->barangay_id) {
                $query->where('barangay_id', $request->barangay_id);
            }

            if ($request->status === 'verified') {
                $query->where('is_verified', 1);
            } elseif ($request->status === 'pending') {
                $query->where('is_verified', 0);
            }

            $beneficiaries = $query->latest()->get();

            // Get paper size and orientation from request (default to A4 portrait)
            $paperSize = $request->input('paper_size', 'A4');
            $orientation = $request->input('orientation', 'portrait');

            $pdf = PDF::loadView('staff.beneficiaries.pdf', compact('beneficiaries'));
            $pdf->setPaper($paperSize, $orientation);
            return $pdf->download('beneficiaries-list.pdf');

        } catch (\Exception $e) {
            return redirect()->route('staff.beneficiaries.index')
                ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    private function createBeneficiaryAccount(Beneficiary $beneficiary)
    {
        $role = Role::where('name', 'Beneficiary')->first();
        if (!$role) return;

        $email    = strtolower($beneficiary->first_name . '.' . $beneficiary->last_name . '@beneficiary.spup.edu.ph');
        $password = 'Benef@' . rand(1000, 9999);

        $user = User::create([
            'role_id'        => $role->id,
            'barangay_id'    => $beneficiary->barangay_id,
            'first_name'     => $beneficiary->first_name,
            'last_name'      => $beneficiary->last_name,
            'email'          => $email,
            'contact_number' => $beneficiary->contact_number ?? 'N/A',
            'password'       => Hash::make($password),
            'status'         => 'active',
        ]);

        $beneficiary->update(['user_id' => $user->id]);
    }
}