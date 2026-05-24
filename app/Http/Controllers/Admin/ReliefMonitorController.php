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
use Barryvdh\DomPDF\Facade\Pdf;

class ReliefMonitorController extends Controller
{
    // Show all relief events
    public function index(Request $request)
    {
        $query = ReliefEvent::with(['eventBarangays.barangay', 'eventBarangays.municipality', 'calamity', 'creator'])
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

        return view('admin.relief.index', compact('events', 'ongoingCount', 'upcomingCount', 'completedCount', 'totalBeneficiaries'));
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

    // Show create form
    public function create(Request $request)
{
    $municipalities = Municipality::with('barangays')->get();
    
    // Load beneficiary counts for each barangay
    foreach ($municipalities as $municipality) {
        foreach ($municipality->barangays as $barangay) {
            $barangay->beneficiary_count = Beneficiary::where('barangay_id', $barangay->id)
                ->where('is_verified', 1)
                ->count();
        }
    }
    $facilitators   = User::with('role')
        ->whereHas('role', fn($q) => $q->whereIn('name', ['Staff', 'Volunteer', 'Barangay Partner']))
        ->get();
    
    $calamities = \App\Models\Calamity::orderBy('name')->get();

    $calamityId   = $request->query('calamity_id');
    $prefillName  = $request->query('name');
    $prefillDate  = $request->query('date');
    $topBarangays = $request->query('top_barangays')
        ? explode(',', $request->query('top_barangays'))
        : [];

    return view('admin.relief.create', compact(
        'municipalities', 'facilitators', 'calamities',
        'calamityId', 'prefillName', 'prefillDate', 'topBarangays'
    ));
}

    // Store new relief event
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
                ReliefEventBeneficiary::firstOrCreate([
                    'relief_event_id' => $event->id,
                    'barangay_id'     => $barangayId,
                    'beneficiary_id'  => $beneficiary->id,
                ]);
            }
        }

        // Attach facilitators
        if ($request->facilitator_ids) {
            // Handle both array and comma-separated string formats
            $facilitatorIds = is_array($request->facilitator_ids) 
                ? $request->facilitator_ids 
                : explode(',', $request->facilitator_ids);
                
            foreach ($facilitatorIds as $userId) {
                $userId = trim($userId);
                if (!empty($userId)) {
                    ReliefEventFacilitator::create([
                        'relief_event_id' => $event->id,
                        'user_id'         => $userId,
                    ]);
                }
            }
        }

        // Save distributed items
        if ($request->distribute_items && $request->item_quantities) {
            $totalBeneficiaries = ReliefEventBeneficiary::where('relief_event_id', $event->id)->count();
            
            foreach ($request->distribute_items as $itemId) {
                $quantity = $request->item_quantities[$itemId] ?? 0;
                if ($quantity > 0) {
                    $item = \App\Models\Item::with('inventory')->find($itemId);
                    $perBeneficiary = $totalBeneficiaries > 0 ? floor($quantity / $totalBeneficiaries) : 0;
                    
                    \App\Models\ReliefEventDistributedItem::create([
                        'relief_event_id' => $event->id,
                        'item_id' => $itemId,
                        'total_quantity' => $quantity,
                        'per_beneficiary' => $perBeneficiary,
                        'beneficiaries_count' => $totalBeneficiaries,
                        'unit' => $item->unit ?? 'pcs',
                    ]);
                }
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
            'distributedItems.item',
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
        
        // Prevent finished events from being marked as ongoing
        if ($event->status === 'Done') {
            return redirect()->route('admin.relief.show', $id)
                ->with('error', 'Cannot mark a finished event as ongoing. Events marked as done cannot be reverted to ongoing status.');
        }
        
        // Validate that the event date is today or has passed
        $eventDate = \Carbon\Carbon::parse($event->date);
        $today = \Carbon\Carbon::today();
        
        if ($eventDate->greaterThan($today)) {
            return redirect()->route('admin.relief.show', $id)
                ->with('error', 'Cannot mark event as ongoing. The event date (' . $eventDate->format('M d, Y') . ') is in the future.');
        }
        
        $event->update(['status' => 'Ongoing']);

        return redirect()->route('admin.relief.show', $id)
            ->with('success', 'Event marked as ongoing.');
    }

    // Update event status (AJAX)
    public function updateStatus(Request $request, $id)
    {
        $event = ReliefEvent::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:Ongoing,Done'
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

        $event->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Event status updated successfully.'
        ]);
    }

    // Delete relief event
    public function destroy($id)
    {
        $event = ReliefEvent::findOrFail($id);
        
        // Prevent deletion of ongoing events
        if ($event->status === 'Ongoing') {
            return redirect()->route('admin.relief.index')
                ->with('error', 'Cannot delete an ongoing event.');
        }

        // Delete related records
        $event->eventBarangays()->delete();
        $event->facilitators()->detach();
        $event->beneficiaries()->delete();
        
        // Delete the event
        $event->delete();

        return redirect()->route('admin.relief.index')
            ->with('success', 'Relief event deleted successfully.');
    }

    // Download relief monitor report as PDF
    public function downloadReport()
    {
        $events = ReliefEvent::with(['eventBarangays.barangay', 'eventBarangays.municipality'])
            ->orderByRaw("FIELD(status, 'Ongoing', 'Upcoming', 'Done')")
            ->latest()
            ->get();

        // Calculate statistics
        $ongoingCount = $events->where('status', 'Ongoing')->count();
        $upcomingCount = $events->where('status', 'Upcoming')->count();
        $completedCount = $events->where('status', 'Done')->count();
        $totalBeneficiaries = ReliefEventBeneficiary::whereIn('relief_event_id', $events->pluck('id'))->count();

        $pdf = Pdf::loadView('admin.relief.pdf.report', compact(
            'events', 
            'ongoingCount', 
            'upcomingCount', 
            'completedCount', 
            'totalBeneficiaries'
        ));

        return $pdf->download('relief-monitor-report-' . now()->format('Y-m-d') . '.pdf');
    }

    // Download individual event report as PDF
    public function downloadEventReport(Request $request, $id)
    {
        $event = ReliefEvent::with([
            'eventBarangays.barangay',
            'eventBarangays.municipality',
            'beneficiaries.beneficiary',
            'creator',
            'calamity',
            'facilitators.role'
        ])->findOrFail($id);

        // Get paper size and orientation from request (default to A4 portrait)
        $paperSize = $request->input('paper_size', 'A4');
        $orientation = $request->input('orientation', 'portrait');

        $pdf = Pdf::loadView('admin.relief.pdf.event', compact('event'));
        $pdf->setPaper($paperSize, $orientation);

        return $pdf->download('relief-event-' . $event->name . '-' . now()->format('Y-m-d') . '.pdf');
    }
}