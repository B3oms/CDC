<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Calamity;
use App\Models\ReliefEvent;
use App\Models\Barangay;
use App\Models\Municipality;
use Illuminate\Http\Request;

class CalamityController extends Controller
{
    public function index()
    {
        $calamities = Calamity::with(['barangays', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('staff.calamities.index', compact('calamities'));
    }

    public function show($id)
    {
        $calamity = Calamity::with([
            'reliefEvents.eventBarangays.barangay',
            'reliefEvents.eventBarangays.municipality',
            'reliefEvents.creator'
        ])->findOrFail($id);

        // Get staff-relevant statistics
        $upcomingEvents = $calamity->reliefEvents->where('status', 'Upcoming')->count();
        $ongoingEvents = $calamity->reliefEvents->where('status', 'Ongoing')->count();
        $completedEvents = $calamity->reliefEvents->where('status', 'Done')->count();
        
        // Get affected areas
        $affectedBarangays = $calamity->reliefEvents
            ->pluck('eventBarangays')
            ->flatten()
            ->pluck('barangay.name')
            ->unique()
            ->sort()
            ->values();

        $affectedMunicipalities = $calamity->reliefEvents
            ->pluck('eventBarangays')
            ->flatten()
            ->pluck('municipality.name')
            ->unique()
            ->sort()
            ->values();

        return view('staff.calamities.show', compact(
            'calamity',
            'upcomingEvents',
            'ongoingEvents', 
            'completedEvents',
            'affectedBarangays',
            'affectedMunicipalities'
        ));
    }
}
