<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use App\Models\ReliefEvent;
use App\Models\ReliefEventBeneficiary;

class ReliefEventController extends Controller
{
    public function index()
    {
        $barangayId = auth()->user()->barangay_id;

        $events = ReliefEvent::with(['eventBarangays.barangay', 'calamity', 'creator'])
            ->whereHas('eventBarangays', function ($q) use ($barangayId) {
                $q->where('barangay_id', $barangayId);
            })
            ->orderByRaw("FIELD(status, 'Ongoing', 'Upcoming', 'Done')")
            ->latest()
            ->get();

        foreach ($events as $event) {
            $event->barangay_beneficiary_count = ReliefEventBeneficiary::where('relief_event_id', $event->id)
                ->where('barangay_id', $barangayId)
                ->count();
        }

        $ongoingCount  = $events->where('status', 'Ongoing')->count();
        $upcomingCount = $events->where('status', 'Upcoming')->count();
        $doneCount     = $events->where('status', 'Done')->count();

        return view('barangay.relief.index', compact(
            'events', 'ongoingCount', 'upcomingCount', 'doneCount'
        ));
    }
}
