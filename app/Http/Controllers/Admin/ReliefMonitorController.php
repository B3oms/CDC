<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReliefEvent;
use App\Models\ReliefEventBarangay;
use App\Models\ReliefEventFacilitator;
use App\Models\ReliefEventBeneficiary;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Http\Request;

class ReliefMonitorController extends Controller
{
    // Show all relief events
    public function index()
    {
        $events = ReliefEvent::with(['eventBarangays.barangay', 'eventBarangays.municipality'])
            ->orderByRaw("FIELD(status, 'Ongoing', 'Upcoming', 'Done')")
            ->latest()
            ->get();

        return view('admin.relief.index', compact('events'));
    }

    // Show create form
    public function create(Request $request)
{
    $municipalities = Municipality::with('barangays')->get();
    $facilitators   = User::with('role')
        ->whereHas('role', fn($q) => $q->whereIn('name', ['Staff', 'Barangay Partner']))
        ->get();

    $calamityId   = $request->query('calamity_id');
    $prefillName  = $request->query('name');
    $prefillDate  = $request->query('date');
    $topBarangays = $request->query('top_barangays')
        ? explode(',', $request->query('top_barangays'))
        : [];

    return view('admin.relief.create', compact(
        'municipalities', 'facilitators',
        'calamityId', 'prefillName', 'prefillDate', 'topBarangays'
    ));
}

    // Store new relief event
    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:150',
            'date'            => 'required|date',
            'venue'           => 'required|string|max:255',
            'barangay_ids'    => 'required|array|min:1',
            'barangay_ids.*'  => 'exists:barangays,id',
            'calamity_id'     => 'nullable|exists:calamities,id',
            'intensity'        => 'nullable|in:low,medium,high,critical',
            'facilitator_ids' => 'nullable|array',
            'facilitator_ids.*' => 'exists:users,id',
        ]);

        $event = ReliefEvent::create([
            'name'        => $request->name,
            'date'        => $request->date,
            'venue'       => $request->venue,
            'status'      => 'Upcoming',
            'calamity_id' => $request->calamity_id ?? null,
            'created_by'  => auth()->id(),
        ]);

        // Attach barangays and auto-pull verified beneficiaries
        foreach ($request->barangay_ids as $barangayId) {
            $barangay = Barangay::find($barangayId);

            ReliefEventBarangay::create([
                'relief_event_id' => $event->id,
                'barangay_id'     => $barangayId,
                'municipality_id' => $barangay->municipality_id,
            ]);

            // Auto-pull verified beneficiaries from this barangay
            $beneficiaries = Beneficiary::where('barangay_id', $barangayId)
                ->where('is_verified', 1)
                ->get();

            foreach ($beneficiaries as $beneficiary) {
                ReliefEventBeneficiary::create([
                    'relief_event_id' => $event->id,
                    'barangay_id'     => $barangayId,
                    'beneficiary_id'  => $beneficiary->id,
                ]);
            }
        }

        // Attach facilitators
        if ($request->facilitator_ids) {
            foreach ($request->facilitator_ids as $userId) {
                ReliefEventFacilitator::create([
                    'relief_event_id' => $event->id,
                    'user_id'         => $userId,
                ]);
            }
        }

        return redirect()->route('admin.relief.show', $event->id)
            ->with('success', 'Relief event created successfully.');
    }

    // Show single event detail
    public function show(Request $request, $id)
    {
        $event = ReliefEvent::with([
            'eventBarangays.barangay',
            'eventBarangays.municipality',
            'facilitators.role',
            'calamity',
        ])->findOrFail($id);

        $barangays = $event->eventBarangays;

        // Filter by barangay if selected
        $selectedBarangayId = $request->query('barangay_id');

        $beneficiaries = ReliefEventBeneficiary::with('beneficiary')
            ->where('relief_event_id', $id)
            ->when($selectedBarangayId, fn($q) => $q->where('barangay_id', $selectedBarangayId))
            ->get();

        return view('admin.relief.show', compact(
            'event', 'barangays', 'beneficiaries', 'selectedBarangayId'
        ));
    }

    // Mark event as done
    public function markDone($id)
    {
        $event = ReliefEvent::findOrFail($id);
        $event->update(['status' => 'Done']);

        return redirect()->route('admin.relief.show', $id)
            ->with('success', 'Event marked as done.');
    }

    // Mark event as ongoing
    public function markOngoing($id)
    {
        $event = ReliefEvent::findOrFail($id);
        $event->update(['status' => 'Ongoing']);

        return redirect()->route('admin.relief.show', $id)
            ->with('success', 'Event marked as ongoing.');
    }
}