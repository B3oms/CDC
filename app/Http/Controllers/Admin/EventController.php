<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReliefEvent;
use App\Models\Beneficiary;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\User;

class EventController extends Controller
{
    public function create()
    {
        $municipalities = Municipality::orderBy('name')->get();
        $facilitators = $this->getCategorizedFacilitators();
        
        return view('admin.events.create', compact(
            'municipalities',
            'facilitators'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
            'venue' => 'required|string|max:255',
            'municipality_id' => 'required|exists:municipalities,id',
            'barangay_ids' => 'required|array|min:1',
            'barangay_ids.*' => 'exists:barangays,id',
            'facilitators' => 'required|array|min:1',
            'facilitators.*.user_id' => 'required|exists:users,id',
            'facilitators.*.position' => 'required|string',
            'estimated_beneficiaries' => 'nullable|integer|min:1'
        ]);

        try {
            $event = ReliefEvent::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'date' => $validated['date'],
                'time' => $validated['time'],
                'venue' => $validated['venue'],
                'municipality_id' => $validated['municipality_id'],
                'status' => 'Upcoming',
                'created_by' => auth()->id(),
                'estimated_beneficiaries' => $validated['estimated_beneficiaries'] ?? null
            ]);

            // Attach barangays
            $event->barangays()->attach($validated['barangay_ids']);

            // Attach facilitators with positions
            foreach ($validated['facilitators'] as $facilitator) {
                $event->facilitators()->attach($facilitator['user_id'], [
                    'position' => $facilitator['position'],
                    'assigned_at' => now(),
                    'assigned_by' => auth()->id()
                ]);
            }

            return redirect()
                ->route('admin.events.index')
                ->with('success', "Relief event '{$event->name}' created successfully with " . count($validated['barangay_ids']) . " barangay(s) assigned.");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create relief event. Please try again.');
        }
    }

    public function getBarangays($municipalityId)
    {
        $barangays = Barangay::where('municipality_id', $municipalityId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($barangays);
    }

    public function getBeneficiaryCounts(Request $request)
    {
        $barangayIds = $request->input('barangay_ids', []);
        
        if (empty($barangayIds)) {
            return response()->json(['total' => 0, 'by_barangay' => []]);
        }

        $counts = Beneficiary::whereIn('barangay_id', $barangayIds)
            ->where('status', 'approved')
            ->selectRaw('barangay_id, COUNT(*) as count')
            ->groupBy('barangay_id')
            ->pluck('count', 'barangay_id')
            ->toArray();

        $total = array_sum($counts);

        return response()->json([
            'total' => $total,
            'by_barangay' => $counts
        ]);
    }

    private function getCategorizedFacilitators()
    {
        return [
            'Executive' => User::whereHas('role', function($query) {
                $query->whereIn('name', ['Mayor', 'Vice Mayor']);
            })->where('status', 'active')->get(['id', 'first_name', 'last_name']),

            'MDRRMO' => User::whereHas('role', function($query) {
                $query->where('name', 'MDRRMO');
            })->where('status', 'active')->get(['id', 'first_name', 'last_name']),

            'Social Welfare' => User::whereHas('role', function($query) {
                $query->where('name', 'Social Welfare Officer');
            })->where('status', 'active')->get(['id', 'first_name', 'last_name']),

            'Barangay Officials' => User::whereHas('role', function($query) {
                $query->whereIn('name', ['Barangay Captain', 'Barangay Kagawad']);
            })->where('status', 'active')->get(['id', 'first_name', 'last_name']),

            'Volunteers' => User::whereHas('role', function($query) {
                $query->where('name', 'Volunteer');
            })->where('status', 'active')->get(['id', 'first_name', 'last_name']),

            'Medical Team' => User::whereHas('role', function($query) {
                $query->where('name', 'Medical Team');
            })->where('status', 'active')->get(['id', 'first_name', 'last_name']),

            'Others' => User::whereHas('role', function($query) {
                $query->where('name', 'Others');
            })->where('status', 'active')->get(['id', 'first_name', 'last_name']),
        ];
    }
}
