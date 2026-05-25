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
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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
        
        $query = Beneficiary::with(['barangay', 'user', 'distributions']);

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

    public function edit($id)
    {
        $beneficiary = Beneficiary::findOrFail($id);
        $municipalities = Municipality::with('barangays')->get();
        $barangays = Barangay::all();
        
        return view('staff.beneficiaries.edit', compact('beneficiary', 'municipalities', 'barangays'));
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
            'age'             => 'required|integer|min:1|max:120',
            'is_4ps_member'   => 'required|boolean',
            'birthdate'       => 'required|date',
            'contact_number'  => 'nullable|string|regex:/^[0-9]{11}$/',
            'address'         => 'nullable|string',
            'family_size'     => 'required|integer|min:1',
            'monthly_income'  => 'required|numeric|min:0',
            'children_count'  => 'required|integer|min:0',
            'has_senior'      => 'required|boolean',
            'interview_notes'  => 'nullable|string',
            // Family background validation
            'mother_name'     => 'nullable|string|max:255',
            'mother_age'      => 'nullable|integer|min:1|max:120',
            'mother_sex'      => 'nullable|in:male,female',
            'mother_birthdate'=> 'nullable|date|before:today',
            'father_name'     => 'nullable|string|max:255',
            'father_age'      => 'nullable|integer|min:1|max:120',
            'father_sex'      => 'nullable|in:male,female',
            'father_birthdate'=> 'nullable|date|before:today',
            'spouse_name'     => 'nullable|string|max:255',
            'spouse_age'      => 'nullable|integer|min:1|max:120',
            'spouse_sex'      => 'nullable|in:male,female',
            'spouse_birthdate'=> 'nullable|date|before:today',
            'spouse_occupation'=> 'nullable|string|max:255',
            'children'        => 'nullable|array',
            'children.*.name' => 'nullable|string|max:255',
            'children.*.age'  => 'nullable|integer|min:0|max:120',
            'children.*.sex'  => 'nullable|in:male,female',
            'children.*.birthdate'=> 'nullable|date|before:today',
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

        // Determine status and rejection logic
        $status = 'pending';
        $rejectionDate = null;
        $scheduledDeletionDate = null;
        
        if ($criteriaMet < 3) {
            $status = 'rejected';
            $rejectionDate = now()->toDateString();
            $scheduledDeletionDate = now()->addDays(10)->toDateString();
        } else {
            $status = 'verified';
        }

        $beneficiary->update([
            'barangay_id'        => $request->barangay_id,
            'first_name'         => $request->first_name,
            'middle_name'        => $request->middle_name,
            'last_name'          => $request->last_name,
            'suffix'             => $request->suffix,
            'gender'             => $request->gender,
            'age'                => $request->age,
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
            // Rejection status fields
            'status'             => $status,
            'rejection_date'     => $rejectionDate,
            'scheduled_deletion_date' => $scheduledDeletionDate,
            // Family background fields
            'mother_name'        => $request->mother_name,
            'mother_age'         => $request->mother_age,
            'mother_sex'         => $request->mother_sex,
            'mother_birthdate'    => $request->mother_birthdate,
            'father_name'        => $request->father_name,
            'father_age'         => $request->father_age,
            'father_sex'         => $request->father_sex,
            'father_birthdate'    => $request->father_birthdate,
            'spouse_name'        => $request->spouse_name,
            'spouse_age'         => $request->spouse_age,
            'spouse_sex'         => $request->spouse_sex,
            'spouse_birthdate'    => $request->spouse_birthdate,
            'spouse_occupation'  => $request->spouse_occupation,
            'children'           => $request->children,
        ]);

        $message = $status === 'verified'
                ? 'Beneficiary verified and updated successfully.'
                : 'Beneficiary rejected for not meeting verification criteria. Record will be automatically deleted after 10 days.';

        return redirect()->route('staff.beneficiaries.index')
            ->with('success', $message);
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
        
        return redirect()->route('staff.beneficiaries.index')
            ->with('success', 'Beneficiary deleted successfully.');
    }

    public function store(Request $request)
    {
        // Check for duplicate beneficiary before validation
        $existingBeneficiary = Beneficiary::where('first_name', $request->first_name)
            ->where('last_name', $request->last_name)
            ->where('birthdate', $request->birthdate)
            ->first();

        if ($existingBeneficiary) {
            return back()
                ->withInput()
                ->withErrors(['duplicate' => 'This person is already registered as a beneficiary.']);
        }

        $request->validate([
            'municipality_id'  => 'required|exists:municipalities,id',
            'barangay_id'     => 'required|exists:barangays,id',
            'region'          => 'required|string',
            'first_name'      => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'middle_name'     => 'nullable|string|max:100|regex:/^[a-zA-Z\s]*$/',
            'last_name'       => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'suffix'          => 'nullable|string|max:20',
            'gender'          => 'required|in:Male,Female,Other',
            'age'             => 'required|integer|min:1|max:120',
            'is_4ps_member'   => 'required|boolean',
            'birthdate'       => 'required|date',
            'contact_number'  => 'nullable|string|regex:/^[0-9]{11}$/',
            'address'         => 'nullable|string',
            'family_size'     => 'required|integer|min:1',
            'monthly_income'  => 'required|numeric|min:0',
            'children_count'  => 'required|integer|min:0',
            'has_senior'      => 'required|boolean',
            'interview_notes'  => 'nullable|string',
            // Family background validation
            'mother_name'     => 'nullable|string|max:255',
            'mother_age'      => 'nullable|integer|min:1|max:120',
            'mother_sex'      => 'nullable|in:male,female',
            'mother_birthdate'=> 'nullable|date|before:today',
            'father_name'     => 'nullable|string|max:255',
            'father_age'      => 'nullable|integer|min:1|max:120',
            'father_sex'      => 'nullable|in:male,female',
            'father_birthdate'=> 'nullable|date|before:today',
            'spouse_name'     => 'nullable|string|max:255',
            'spouse_age'      => 'nullable|integer|min:1|max:120',
            'spouse_sex'      => 'nullable|in:male,female',
            'spouse_birthdate'=> 'nullable|date|before:today',
            'spouse_occupation'=> 'nullable|string|max:255',
            'children'        => 'nullable|array',
            'children.*.name' => 'nullable|string|max:255',
            'children.*.age'  => 'nullable|integer|min:0|max:120',
            'children.*.sex'  => 'nullable|in:male,female',
            'children.*.birthdate'=> 'nullable|date|before:today',
        ], [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'birthdate.required' => 'Birthdate is required.',
            'age.required' => 'Age is required.',
            'age.min' => 'Age must be at least 1.',
            'age.max' => 'Age must not exceed 120.',
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

        // Determine status and rejection logic
        $status = 'pending';
        $rejectionDate = null;
        $scheduledDeletionDate = null;
        
        if ($criteriaMet < 3) {
            $status = 'rejected';
            $rejectionDate = now()->toDateString();
            $scheduledDeletionDate = now()->addDays(10)->toDateString();
        } else {
            $status = 'verified';
        }

        // Generate unique ID
        $uniqueId = $this->generateUniqueId($request->barangay_id);

        try {
            $beneficiary = Beneficiary::create([
                'barangay_id'        => $request->barangay_id,
                'unique_id'          => $uniqueId,
                'first_name'         => $request->first_name,
                'middle_name'        => $request->middle_name,
                'last_name'          => $request->last_name,
                'suffix'             => $request->suffix,
                'gender'             => $request->gender,
                'age'                => $request->age,
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
                // Family background fields
                'mother_name'        => $request->mother_name,
                'mother_age'         => $request->mother_age,
                'mother_sex'         => $request->mother_sex,
                'mother_birthdate'    => $request->mother_birthdate,
                'father_name'        => $request->father_name,
                'father_age'         => $request->father_age,
                'father_sex'         => $request->father_sex,
                'father_birthdate'    => $request->father_birthdate,
                'spouse_name'        => $request->spouse_name,
                'spouse_age'         => $request->spouse_age,
                'spouse_sex'         => $request->spouse_sex,
                'spouse_birthdate'    => $request->spouse_birthdate,
                'spouse_occupation'  => $request->spouse_occupation,
                'children'           => $request->children,
                // Rejection status fields
                'status'             => $status,
                'rejection_date'     => $rejectionDate,
                'scheduled_deletion_date' => $scheduledDeletionDate,
            ]);

            // Create beneficiary account if verified
            if ($isVerified) {
                $this->createBeneficiaryAccount($beneficiary);
            }

            // Trigger notification for beneficiary addition
            NotificationService::beneficiaryAdded($beneficiary->id, auth()->id());

            // If converted from a recommendation, mark it and notify the barangay partner
            if ($request->recommended_id) {
                $recommended = \App\Models\RecommendedBeneficiary::find($request->recommended_id);
                if ($recommended) {
                    $recommended->update(['status' => 'Converted']);
                    NotificationService::recommendationConverted($recommended->id);
                }
            }

            $message = $status === 'verified'
                ? 'Beneficiary verified and added successfully.'
                : 'Beneficiary rejected for not meeting verification criteria. Record will be automatically deleted after 10 days.';

            return redirect()->route('staff.beneficiaries.index')
                ->with('success', $message);

        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate entry error (error code 1062)
            if ($e->getCode() == 1062 || $e->errorInfo[1] == 1062) {
                return back()
                    ->withInput()
                    ->withErrors(['duplicate' => 'This person is already registered as a beneficiary.']);
            }

            // Re-throw other database errors
            throw $e;
        }
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
            // Get paper size and orientation from request (default to A4 portrait)
            $paperSize = $request->input('paper_size', 'A4');
            $orientation = $request->input('orientation', 'portrait');

            // Build query with same filters as index method
            $beneficiaries = Beneficiary::with('barangay.municipality')
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
                    if ($request->status === 'verified') {
                        $q->where('is_verified', 1);
                    } elseif ($request->status === 'pending') {
                        $q->where('is_verified', 0);
                    }
                })
                ->orderBy('created_at', 'desc')
                ->get(); // No pagination for PDF

            // Generate PDF using Barryvdh DomPDF
            $pdf = Pdf::loadView('staff.beneficiaries.pdf', compact('beneficiaries'));
            $pdf->setPaper($paperSize, $orientation);
            
            // Generate filename with current date
            $filename = 'beneficiaries-' . Carbon::now()->format('Y-m-d') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            // Log error and return with error message
            \Log::error('PDF generation failed: ' . $e->getMessage());
            
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

    /**
     * Generate a unique ID for beneficiary
     */
    private function generateUniqueId($barangayId)
    {
        // Get barangay code
        $barangay = Barangay::find($barangayId);
        $barangayCode = $barangay ? strtoupper(substr($barangay->name, 0, 3)) : 'BRG';
        
        // Get current year
        $year = date('Y');
        
        // Get sequential number for this barangay
        $lastBeneficiary = Beneficiary::where('barangay_id', $barangayId)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastBeneficiary ? ((int) substr($lastBeneficiary->unique_id ?? '000', -3) + 1) : 1;
        
        // Format: BRG-CODE-YEAR-NNN (e.g., BRG-MNL-2026-001)
        return sprintf('%s-%s-%s-%03d', $barangayCode, 'SPUP', $year, $sequence);
    }
}