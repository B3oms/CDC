<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use App\Models\Calamity;
use App\Models\EvacuationCenter;
use App\Models\EvacuationReport;
use App\Models\CalamityPartner;
use App\Models\HouseholdRequest;
use Illuminate\Http\Request;

class EvacuationController extends Controller
{
    // Barangay dashboard — shows active calamity portal
    public function index()
    {
        $barangayId = auth()->user()->barangay_id;

        $activeCalamity = Calamity::where('status', 'Open')
            ->whereHas('partners', function($q) use ($barangayId) {
                $q->where('barangay_id', $barangayId);
            })
            ->latest()
            ->first();

        // Get evacuation center for this barangay
        $evacuationCenter = EvacuationCenter::where('calamity_id', $activeCalamity->id ?? 0)
            ->where('barangay_id', $barangayId)
            ->first();
            
        // Get latest report for this barangay
        $latestReport = EvacuationReport::where('calamity_id', $activeCalamity->id ?? 0)
            ->where('barangay_id', $barangayId)
            ->first();

        // Get approved households for this barangay
        try {
            $households = HouseholdRequest::where('barangay_id', $barangayId)
                ->where('status', 'approved')
                ->orderBy('head_of_household')
                ->get();
        } catch (\Exception $e) {
            // Log the error and provide empty collection as fallback
            \Log::error('Error fetching households: ' . $e->getMessage());
            $households = collect();
        }

        return view('barangay.dashboard', compact(
            'activeCalamity',
            'evacuationCenter', 
            'latestReport',
            'households'
        ));
    }

    // Set evacuation center
    public function setCenter(Request $request)
    {
        $request->validate([
            'calamity_id' => 'required|exists:calamities,id',
            'venue'       => 'required|string|max:255',
            'location'    => 'required|string|max:255',
        ]);

        $barangayId = auth()->user()->barangay_id;

        EvacuationCenter::updateOrCreate(
            [
                'calamity_id' => $request->calamity_id,
                'barangay_id' => $barangayId,
            ],
            [
                'venue'    => $request->venue,
                'location' => $request->location,
            ]
        );

        return back()->with('success', 'Evacuation center updated.');
    }

    // Submit/update report
    public function submitReport(Request $request)
    {
        $request->validate([
            'calamity_id'         => 'required|exists:calamities,id',
            'evacuation_center_id'=> 'required|exists:evacuation_centers,id',
            'household_ids'       => 'required|array',
            'household_ids.*'     => 'exists:household_requests,id',
        ]);

        $barangayId = auth()->user()->barangay_id;
        
        // Get existing report to accumulate households
        $existingReport = EvacuationReport::where('calamity_id', $request->calamity_id)
            ->where('barangay_id', $barangayId)
            ->first();

        // Get existing household IDs or start with empty array
        $existingHouseholdIds = [];
        if ($existingReport && $existingReport->household_ids) {
            $existingData = $existingReport->household_ids;
            if (is_string($existingData)) {
                $existingHouseholdIds = json_decode($existingData, true) ?: [];
            } elseif (is_array($existingData)) {
                $existingHouseholdIds = $existingData;
            }
        }

        // Merge existing and new household IDs, removing duplicates
        $allHouseholdIds = array_unique(array_merge($existingHouseholdIds, $request->household_ids));
        
        // Calculate household count from all accumulated households
        $householdCount = count($allHouseholdIds);

        // Calculate total evacuee count by summing family sizes of all accumulated households
        $totalEvacueeCount = 0;
        if (!empty($allHouseholdIds)) {
            $households = HouseholdRequest::whereIn('id', $allHouseholdIds)->get(['family_size']);
            $totalEvacueeCount = $households->sum('family_size');
        }

        // Update scoring calculation without severity level
        $score = ($totalEvacueeCount * 0.7) + ($householdCount * 0.3);

        EvacuationReport::updateOrCreate(
            [
                'calamity_id' => $request->calamity_id,
                'barangay_id' => $barangayId,
            ],
            [
                'evacuation_center_id' => $request->evacuation_center_id,
                'reported_by'          => auth()->id(),
                'household_count'      => $householdCount,
                'household_ids'        => json_encode($allHouseholdIds),
                'evacuee_count'        => $totalEvacueeCount,
                'severity_level'       => 1, // Default value since field is removed
                'ranking_score'        => $score,
            ]
        );

        return back()->with('success', 'Report updated successfully.');
    }
}