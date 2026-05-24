<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InterviewController extends Controller
{
    /**
     * Show the beneficiary interview form
     */
    public function create()
    {
        return view('beneficiary.interview');
    }

    /**
     * Store the beneficiary interview form
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Mother Information
            'mother_name' => 'required|string|max:255',
            'mother_age' => 'required|integer|min:1|max:120',
            'mother_sex' => 'required|in:male,female',
            'mother_birthdate' => 'required|date|before:today',

            // Father Information
            'father_name' => 'required|string|max:255',
            'father_age' => 'required|integer|min:1|max:120',
            'father_sex' => 'required|in:male,female',
            'father_birthdate' => 'required|date|before:today',

            // Spouse Information (optional)
            'spouse_name' => 'nullable|string|max:255',
            'spouse_age' => 'nullable|integer|min:1|max:120',
            'spouse_sex' => 'nullable|in:male,female',
            'spouse_birthdate' => 'nullable|date|before:today',
            'spouse_occupation' => 'nullable|string|max:255',

            // Children Information
            'children' => 'required|array|min:1',
            'children.*.name' => 'required|string|max:255',
            'children.*.age' => 'required|integer|min:0|max:120',
            'children.*.sex' => 'required|in:male,female',
            'children.*.birthdate' => 'required|date|before:today',
        ], [
            'mother_name.required' => 'Mother\'s name is required',
            'mother_age.required' => 'Mother\'s age is required',
            'mother_age.integer' => 'Mother\'s age must be a number',
            'mother_age.min' => 'Mother\'s age must be at least 1',
            'mother_age.max' => 'Mother\'s age must not exceed 120',
            'mother_sex.required' => 'Mother\'s sex is required',
            'mother_birthdate.required' => 'Mother\'s birthdate is required',
            'mother_birthdate.before' => 'Mother\'s birthdate must be before today',

            'father_name.required' => 'Father\'s name is required',
            'father_age.required' => 'Father\'s age is required',
            'father_age.integer' => 'Father\'s age must be a number',
            'father_age.min' => 'Father\'s age must be at least 1',
            'father_age.max' => 'Father\'s age must not exceed 120',
            'father_sex.required' => 'Father\'s sex is required',
            'father_birthdate.required' => 'Father\'s birthdate is required',
            'father_birthdate.before' => 'Father\'s birthdate must be before today',

            'spouse_age.integer' => 'Spouse\'s age must be a number',
            'spouse_age.min' => 'Spouse\'s age must be at least 1',
            'spouse_age.max' => 'Spouse\'s age must not exceed 120',
            'spouse_birthdate.before' => 'Spouse\'s birthdate must be before today',

            'children.required' => 'At least one child must be added',
            'children.min' => 'At least one child must be added',
            'children.*.name.required' => 'Child\'s name is required',
            'children.*.age.required' => 'Child\'s age is required',
            'children.*.age.integer' => 'Child\'s age must be a number',
            'children.*.age.min' => 'Child\'s age must be at least 0',
            'children.*.age.max' => 'Child\'s age must not exceed 120',
            'children.*.sex.required' => 'Child\'s sex is required',
            'children.*.birthdate.required' => 'Child\'s birthdate is required',
            'children.*.birthdate.before' => 'Child\'s birthdate must be before today',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Get the authenticated user
            $user = Auth::user();
            
            // Prepare interview data
            $interviewData = [
                'user_id' => $user->id,
                'mother' => [
                    'name' => $request->mother_name,
                    'age' => $request->mother_age,
                    'sex' => $request->mother_sex,
                    'birthdate' => $request->mother_birthdate,
                ],
                'father' => [
                    'name' => $request->father_name,
                    'age' => $request->father_age,
                    'sex' => $request->father_sex,
                    'birthdate' => $request->father_birthdate,
                ],
                'spouse' => null,
                'children' => [],
                'interview_date' => now()->toDateString(),
                'status' => 'completed',
            ];

            // Add spouse information if provided
            if ($request->filled('spouse_name')) {
                $interviewData['spouse'] = [
                    'name' => $request->spouse_name,
                    'age' => $request->spouse_age,
                    'sex' => $request->spouse_sex,
                    'birthdate' => $request->spouse_birthdate,
                    'occupation' => $request->spouse_occupation,
                ];
            }

            // Add children information
            foreach ($request->children as $child) {
                $interviewData['children'][] = [
                    'name' => $child['name'],
                    'age' => $child['age'],
                    'sex' => $child['sex'],
                    'birthdate' => $child['birthdate'],
                ];
            }

            // For now, we'll store the interview data in the session
            // In a real implementation, you might want to create a database table for interviews
            session(['beneficiary_interview' => $interviewData]);

            return redirect()
                ->route('beneficiary.dashboard')
                ->with('success', 'Beneficiary interview form submitted successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to submit interview form: ' . $e->getMessage());
        }
    }

    /**
     * Show the interview results
     */
    public function show()
    {
        $interviewData = session('beneficiary_interview');
        
        if (!$interviewData) {
            return redirect()
                ->route('beneficiary.interview.create')
                ->with('error', 'No interview data found. Please complete the interview form first.');
        }

        return view('beneficiary.interview-show', compact('interviewData'));
    }
}
