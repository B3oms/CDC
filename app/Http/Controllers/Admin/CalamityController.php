<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Calamity;
use App\Models\CalamityPartner;
use App\Models\Barangay;
use App\Models\Municipality;
use App\Models\EvacuationReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalamityController extends Controller
{
    // Show calamity index
    public function index()
    {
        $calamities = Calamity::with(['barangays', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.calamity.index', compact('calamities'));
    }

    // Show add event form
    public function create()
    {
        $municipalities = Municipality::with('barangays')->get();
        return view('admin.calamity.create', compact('municipalities'));
    }

    // Store new calamity event
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:150',
            'type'          => 'required|string|max:100',
            'description'   => 'nullable|string',
            'date_occurred' => 'required|date',
            'barangay_ids'  => 'required|array|min:1',
            'barangay_ids.*'=> 'exists:barangays,id',
        ]);

        $calamity = Calamity::create([
            'name'          => $request->name,
            'type'          => $request->type,
            'intensity'     => $request->intensity,
            'description'   => $request->description,
            'date_occurred' => $request->date_occurred,
            'status'        => 'Open',
            'created_by'    => auth()->id(),
        ]);

        foreach ($request->barangay_ids as $barangayId) {
            CalamityPartner::create([
                'calamity_id' => $calamity->id,
                'barangay_id' => $barangayId,
            ]);
        }

        return redirect()->route('admin.calamity.show', $calamity->id)
            ->with('success', 'Calamity event created successfully.');
    }

    // Show calamity portal with rankings
    public function show($id)
    {
        $calamity = Calamity::with(['barangays', 'evacuationReports.barangay'])
            ->findOrFail($id);

        // Get rankings
        $rankings = EvacuationReport::where('calamity_id', $id)
            ->select('barangay_id',
                \DB::raw('SUM(evacuee_count) as total_evacuees'),
                \DB::raw('SUM(household_count) as total_households'),
                \DB::raw('MAX(severity_level) as max_severity'),
                \DB::raw('(SUM(evacuee_count) * 0.6 + SUM(household_count) * 0.2 + MAX(severity_level) * 0.2) as score')
            )
            ->groupBy('barangay_id')
            ->orderByDesc('score')
            ->limit(10)
            ->with('barangay')
            ->get();

        return view('admin.calamity.show', compact('calamity', 'rankings'));
    }

    // Close portal
    public function close($id)
    {
        try {
            $calamity = Calamity::findOrFail($id);
            $calamity->update(['status' => 'Closed']);
            
            return redirect()->route('admin.calamity.index')
                ->with('success', 'Calamity portal closed successfully.');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.calamity.index')
                ->with('error', 'Failed to close calamity portal: ' . $e->getMessage());
        }
    }

    // Show final report
    public function report($id)
    {
        $calamity = Calamity::findOrFail($id);

        $rankings = EvacuationReport::where('calamity_id', $id)
            ->select('barangay_id',
                \DB::raw('SUM(evacuee_count) as total_evacuees'),
                \DB::raw('SUM(household_count) as total_households'),
                \DB::raw('MAX(severity_level) as max_severity'),
                \DB::raw('(SUM(evacuee_count) * 0.6 + SUM(household_count) * 0.2 + MAX(severity_level) * 0.2) as score')
            )
            ->groupBy('barangay_id')
            ->orderByDesc('score')
            ->limit(10)
            ->with('barangay')
            ->get();

        return view('admin.calamity.report', compact('calamity', 'rankings'));
    }
}