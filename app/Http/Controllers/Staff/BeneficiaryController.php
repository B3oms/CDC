<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\Barangay;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BeneficiaryController extends Controller
{
    public function index(Request $request)
    {
        $barangays = Barangay::all();
        $query     = Beneficiary::with('barangay');

        if ($request->barangay_id) {
            $query->where('barangay_id', $request->barangay_id);
        }

        if ($request->status === 'verified') {
            $query->where('is_verified', 1);
        } elseif ($request->status === 'pending') {
            $query->where('is_verified', 0);
        }

        $beneficiaries = $query->latest()->paginate(20);

        // Slot counts per barangay
        $slotCounts = Beneficiary::where('is_verified', 1)
            ->selectRaw('barangay_id, COUNT(*) as count')
            ->groupBy('barangay_id')
            ->pluck('count', 'barangay_id');

        return view('staff.beneficiaries.index', compact(
            'beneficiaries', 'barangays', 'slotCounts'
        ));
    }

    public function create()
    {
        $barangays = Barangay::all();
        return view('staff.beneficiaries.create', compact('barangays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barangay_id'     => 'required|exists:barangays,id',
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'gender'          => 'required|in:Male,Female,Other',
            'birthdate'       => 'required|date',
            'contact_number'  => 'nullable|string|max:13',
            'address'         => 'nullable|string',
            'family_size'     => 'required|integer|min:1',
            'monthly_income'  => 'required|numeric|min:0',
            'has_senior'      => 'nullable|boolean',
            'children_count'  => 'required|integer|min:0',
            'interview_notes' => 'nullable|string',
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
            $request->children_count
        );

        $isVerified       = $criteriaMet >= 2 ? 1 : 0;
        $vulnerabilityLevel = match(true) {
            $criteriaMet >= 3 => 'High',
            $criteriaMet == 2 => 'Medium',
            default           => 'Low',
        };

        $beneficiary = Beneficiary::create([
            'barangay_id'        => $request->barangay_id,
            'first_name'         => $request->first_name,
            'last_name'          => $request->last_name,
            'gender'             => $request->gender,
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