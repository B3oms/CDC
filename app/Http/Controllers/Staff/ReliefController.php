<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ReliefEvent;
use App\Models\ReliefEventBeneficiary;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ReliefController extends Controller
{
    // Show all relief events
    public function index()
    {
        $events = ReliefEvent::with(['eventBarangays.barangay', 'eventBarangays.municipality', 'creator', 'calamity'])
            ->orderByRaw("FIELD(status, 'Ongoing', 'Upcoming', 'Done')")
            ->latest()
            ->get();

        // Load beneficiary counts for each event
        foreach ($events as $event) {
            // Ensure eventBarangays are loaded
            if ($event->eventBarangays->isEmpty()) {
                // Try to load eventBarangays if not already loaded
                $event->load('eventBarangays.barangay');
            }
            
            foreach ($event->eventBarangays as $eventBarangay) {
                $eventBarangay->beneficiary_count = ReliefEventBeneficiary::where('relief_event_id', $event->id)
                    ->where('barangay_id', $eventBarangay->barangay_id)
                    ->count();
            }
        }

        // Calculate statistics
        $ongoingCount = $events->where('status', 'Ongoing')->count();
        $upcomingCount = $events->where('status', 'Upcoming')->count();
        $completedCount = $events->where('status', 'Done')->count();
        
        // Calculate total beneficiaries
        $totalBeneficiaries = ReliefEventBeneficiary::whereIn('relief_event_id', $events->pluck('id'))->count();

        return view('staff.relief.index', compact('events', 'ongoingCount', 'upcomingCount', 'completedCount', 'totalBeneficiaries'));
    }

    // Create relief event
    public function create()
    {
        $municipalities = \App\Models\Municipality::with('barangays')->get();
        
        // Load beneficiary counts for each barangay
        foreach ($municipalities as $municipality) {
            foreach ($municipality->barangays as $barangay) {
                $barangay->beneficiary_count = \App\Models\Beneficiary::where('barangay_id', $barangay->id)
                    ->where('is_verified', 1)
                    ->count();
            }
        }
        
        // Try a simpler approach - load all users and check roles
        $allUsers = \App\Models\User::all();
        $facilitators = collect();
        
        foreach ($allUsers as $user) {
            if ($user->role_id) {
                $user->load('role');
                $facilitators->push($user);
            }
        }
        
        // Debug: Log what we found
        \Log::info('Total users: ' . $allUsers->count());
        \Log::info('Users with role_id: ' . $facilitators->count());
        foreach ($facilitators as $f) {
            \Log::info('User: ' . $f->first_name . ' ' . $f->last_name . ' - Role: ' . ($f->role ? $f->role->name : 'No role'));
        }

        // Load calamities for the dropdown
        $calamities = \App\Models\Calamity::orderBy('name')->get();
        
        // Handle prefill from calamity
        $calamityId = request('calamity_id');
        $prefillName = '';
        $prefillDate = '';
        $prefillVenue = '';
        
        if ($calamityId) {
            $calamity = \App\Models\Calamity::find($calamityId);
            if ($calamity) {
                $prefillName = $calamity->name . ' Response';
                $prefillDate = now()->format('Y-m-d');
                $prefillVenue = 'Evacuation Center';
            }
        }

        return view('staff.relief.create', compact(
            'municipalities', 'facilitators', 'calamities', 'calamityId', 'prefillDate', 'prefillVenue', 'prefillName'
        ));
    }

    // Get real-time statistics
    public function stats()
    {
        $events = ReliefEvent::orderBy('date', 'desc')->get();
        
        $ongoingCount = $events->where('status', 'Ongoing')->count();
        $upcomingCount = $events->where('status', 'Upcoming')->count();
        $completedCount = $events->where('status', 'Done')->count();
        
        // Calculate total beneficiaries
        $totalBeneficiaries = ReliefEventBeneficiary::whereIn('relief_event_id', $events->pluck('id'))->count();

        return response()->json([
            'ongoingCount' => $ongoingCount,
            'upcomingCount' => $upcomingCount,
            'completedCount' => $completedCount,
            'totalBeneficiaries' => $totalBeneficiaries,
            'lastUpdated' => 'just now'
        ]);
    }

    // Show relief event details
    public function show($id)
    {
        $event = ReliefEvent::with(['eventBarangays.barangay', 'eventBarangays.municipality', 'beneficiaries', 'facilitators', 'calamity'])
            ->findOrFail($id);

        // Filter beneficiaries by barangay if specified
        if (request('barangay_id')) {
            $event->beneficiaries = $event->beneficiaries->where('barangay_id', request('barangay_id'));
        }

        return view('staff.relief.show', compact('event'));
    }

    // Store relief event
    public function store(Request $request)
    {
        if (in_array($request->calamity_id, ['natural', 'human_made'], true)) {
            $request->merge(['calamity_id' => null]);
        }

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
            $barangay = \App\Models\Barangay::find($barangayId);

            \App\Models\ReliefEventBarangay::create([
                'relief_event_id' => $event->id,
                'barangay_id'     => $barangayId,
                'municipality_id' => $barangay->municipality_id,
            ]);

            // Auto-pull verified beneficiaries from this barangay
            $beneficiaries = \App\Models\Beneficiary::where('barangay_id', $barangayId)
                ->where('is_verified', 1)
                ->get();

            foreach ($beneficiaries as $beneficiary) {
                \App\Models\ReliefEventBeneficiary::firstOrCreate([
                    'relief_event_id' => $event->id,
                    'barangay_id'     => $barangayId,
                    'beneficiary_id'  => $beneficiary->id,
                ]);
            }
        }

        // Attach facilitators
        if ($request->facilitator_ids) {
            $facilitatorIds = $request->facilitator_ids;
                
            foreach ($facilitatorIds as $userId) {
                if (!empty($userId)) {
                    \App\Models\ReliefEventFacilitator::create([
                        'relief_event_id' => $event->id,
                        'user_id'         => $userId,
                    ]);
                }
            }
        }

        // Trigger notification for event creation
        NotificationService::eventCreated($event->id, auth()->id());

        return redirect()->route('staff.relief.show', $event->id)
            ->with('success', 'Relief event created successfully.');
    }

    // Delete relief event
    public function destroy($id)
    {
        $event = ReliefEvent::findOrFail($id);
        
        // Check if event can be deleted (only if not ongoing)
        if ($event->status === 'Ongoing') {
            return redirect()->back()
                ->with('error', 'Cannot delete an ongoing relief event.');
        }
        
        // Delete related records
        $event->eventBarangays()->delete();
        $event->beneficiaries()->delete();
        // Only delete the pivot table records for facilitators, not the users themselves
        $event->facilitators()->detach();
        
        // Delete the event
        $event->delete();
        
        return redirect()->route('staff.relief.index')
            ->with('success', 'Relief event deleted successfully.');
    }

    // Update event status
    public function updateStatus(Request $request, $id)
    {
        $event = ReliefEvent::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:Ongoing,Done,Upcoming'
        ]);

        $event->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event status updated successfully',
            'new_status' => $request->status
        ]);
    }
}
