<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ReliefEvent;
use App\Models\ReliefEventBeneficiary;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReliefController extends Controller
{
    // Show all relief events
    public function index(Request $request)
    {
        $query = ReliefEvent::with(['eventBarangays.barangay', 'eventBarangays.municipality', 'creator', 'calamity'])
            ->orderByRaw("FIELD(status, 'Ongoing', 'Upcoming', 'Done')")
            ->latest();

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        // Filter by calamity search
        if ($request->calamity_search) {
            $query->whereHas('calamity', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->calamity_search . '%');
            })->whereNotNull('calamity_id');
        }

        $events = $query->get();

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
        $event = ReliefEvent::with([
            'eventBarangays.barangay',
            'eventBarangays.municipality',
            'facilitators.role',
            'calamity',
            'distributedItems.item',
        ])->findOrFail($id);

        $barangays = $event->eventBarangays;

        // Filter by barangay if selected
        $selectedBarangayId = request('barangay_id');

        $beneficiaries = \App\Models\ReliefEventBeneficiary::with('beneficiary')
            ->where('relief_event_id', $id)
            ->when($selectedBarangayId, fn($q) => $q->where('barangay_id', $selectedBarangayId))
            ->get();

        return view('staff.relief.show', compact(
            'event', 'barangays', 'beneficiaries', 'selectedBarangayId'
        ));
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
            'distribute_items' => 'nullable|array',
            'distribute_items.*' => 'exists:items,id',
            'item_quantities' => 'nullable|array',
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

        // Save distributed items
        if ($request->distribute_items && $request->item_quantities) {
            $totalBeneficiaries = \App\Models\ReliefEventBeneficiary::where('relief_event_id', $event->id)->count();
            
            foreach ($request->distribute_items as $itemId) {
                $quantity = $request->item_quantities[$itemId] ?? 0;
                if ($quantity > 0) {
                    $item = \App\Models\Item::with('inventory')->find($itemId);
                    $perBeneficiary = $totalBeneficiaries > 0 ? floor($quantity / $totalBeneficiaries) : 0;
                    
                    // Check if enough inventory is available
                    $availableQuantity = $item->inventory?->quantity ?? 0;
                    if ($availableQuantity >= $quantity) {
                        // Deduct from inventory
                        if ($item->inventory) {
                            $item->inventory->decrement('quantity', $quantity);
                            $item->inventory->update(['last_updated' => now()]);
                        }
                        
                        \App\Models\ReliefEventDistributedItem::create([
                            'relief_event_id' => $event->id,
                            'item_id' => $itemId,
                            'total_quantity' => $quantity,
                            'per_beneficiary' => $perBeneficiary,
                            'beneficiaries_count' => $totalBeneficiaries,
                            'unit' => $item->unit ?? 'pcs',
                        ]);
                    } else {
                        // Skip items with insufficient inventory
                        continue;
                    }
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

        // Prevent finished events from being marked as ongoing
        if ($request->status === 'Ongoing' && $event->status === 'Done') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot mark a finished event as ongoing. Events marked as done cannot be reverted to ongoing status.'
            ]);
        }

        // Validate that the event date is today or has passed if trying to mark as ongoing
        if ($request->status === 'Ongoing') {
            $eventDate = \Carbon\Carbon::parse($event->date);
            $today = \Carbon\Carbon::today();
            
            if ($eventDate->greaterThan($today)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot mark event as ongoing. The event date (' . $eventDate->format('M d, Y') . ') is in the future.'
                ]);
            }
        }

        $event->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event status updated successfully',
            'new_status' => $request->status
        ]);
    }

    // Download relief event PDF
    public function downloadPDF(Request $request, $id)
    {
        try {
            // Get the actual event data from the database
            $event = ReliefEvent::with([
                'eventBarangays.barangay',
                'eventBarangays.municipality',
                'beneficiaries',
                'creator',
                'calamity',
                'facilitators.role'
            ])->findOrFail($id);

            // Get paper size and orientation from request (default to A4 portrait)
            $paperSize = $request->input('paper_size', 'A4');
            $orientation = $request->input('orientation', 'portrait');

            $pdf = PDF::loadView('staff.relief.pdf', compact('event'));
            $pdf->setPaper($paperSize, $orientation);
            return $pdf->download('relief-event-' . $event->name . '-' . now()->format('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            \Log::error('PDF Generation Trace: ' . $e->getTraceAsString());

            return redirect()->route('staff.relief.show', $id)
                ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    // Fetch real-time statistics
    public function getStats()
    {
        $events = ReliefEvent::all();
        
        $stats = [
            'ongoingCount' => $events->where('status', 'Ongoing')->count(),
            'upcomingCount' => $events->where('status', 'Upcoming')->count(),
            'completedCount' => $events->where('status', 'Done')->count(),
            'totalBeneficiaries' => ReliefEventBeneficiary::whereIn('relief_event_id', $events->pluck('id'))->count(),
            'lastUpdated' => now()->format('M d, Y H:i:s')
        ];

        return response()->json($stats);
    }

    // Download individual event report as PDF
    public function pdf(Request $request, $id)
    {
        $event = ReliefEvent::with([
            'eventBarangays.barangay',
            'eventBarangays.municipality',
            'distributedItems.item',
            'beneficiaries.beneficiary',
            'creator',
            'calamity',
            'facilitators.role'
        ])->findOrFail($id);

        // Get paper size and orientation from request (default to A4 portrait)
        $paperSize = $request->input('paper_size', 'A4');
        $orientation = $request->input('orientation', 'portrait');

        try {
            $pdf = Pdf::loadView('staff.relief.pdf', compact('event'));
            $pdf->setPaper($paperSize, $orientation);
            return $pdf->download('relief-event-' . $event->name . '-' . now()->format('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            \Log::error('PDF Generation Trace: ' . $e->getTraceAsString());

            return redirect()->route('staff.relief.show', $id)
                ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
}
