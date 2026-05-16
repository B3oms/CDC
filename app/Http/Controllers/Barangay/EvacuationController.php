<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use App\Models\Calamity;
use App\Models\EvacuationCenter;
use App\Models\EvacuationReport;
use App\Models\ReliefOperation;
use App\Models\ReliefOperationFeedback;
use App\Services\NotificationService;
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

        $evacuationCenter = null;
        $latestReport = null;
        $rankings = null;

        if ($activeCalamity) {
            $evacuationCenter = EvacuationCenter::where('calamity_id', $activeCalamity->id)
                ->where('barangay_id', $barangayId)
                ->first();

            $latestReport = EvacuationReport::where('calamity_id', $activeCalamity->id)
                ->where('barangay_id', $barangayId)
                ->latest()
                ->first();

            $rankings = EvacuationReport::where('calamity_id', $activeCalamity->id)
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
        }

        $reliefHistory = ReliefOperation::with('calamity')
            ->withCount('feedbacks')
            ->where('barangay_id', $barangayId)
            ->orderByDesc('operation_date')
            ->limit(10)
            ->get();

        $recentFeedbacks = ReliefOperationFeedback::with('reliefOperation.calamity')
            ->where('barangay_id', $barangayId)
            ->latest()
            ->limit(5)
            ->get();

        return view('barangay.dashboard', compact(
            'activeCalamity', 'evacuationCenter', 'latestReport', 'rankings', 'reliefHistory', 'recentFeedbacks'
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
            'household_count'     => 'required|integer|min:0',
            'evacuee_count'       => 'required|integer|min:0',
            'severity_level'      => 'required|in:1,2,3,4,5',
        ]);

        $barangayId = auth()->user()->barangay_id;

        $score = ($request->evacuee_count * 0.6)
               + ($request->household_count * 0.2)
               + ($request->severity_level * 0.2);

        EvacuationReport::updateOrCreate(
            [
                'calamity_id' => $request->calamity_id,
                'barangay_id' => $barangayId,
            ],
            [
                'evacuation_center_id' => $request->evacuation_center_id,
                'reported_by'          => auth()->id(),
                'household_count'      => $request->household_count,
                'evacuee_count'        => $request->evacuee_count,
                'severity_level'       => $request->severity_level,
                'ranking_score'        => $score,
            ]
        );

        return back()->with('success', 'Report updated successfully.');
    }

    // Submit feedback for a relief operation
    public function submitFeedback(Request $request)
    {
        $request->validate([
            'relief_operation_id' => 'required|exists:relief_operations,id',
            'message' => 'required|string|max:1000',
        ]);

        $barangayId = auth()->user()->barangay_id;

        $reliefOperation = ReliefOperation::where('id', $request->relief_operation_id)
            ->where('barangay_id', $barangayId)
            ->firstOrFail();

        $feedback = ReliefOperationFeedback::create([
            'relief_operation_id' => $reliefOperation->id,
            'barangay_id' => $barangayId,
            'message' => $request->message,
            'created_by' => auth()->id(),
        ]);

        NotificationService::barangayFeedbackSubmitted($feedback->id, auth()->id());

        return back()->with('success', 'Thank you for your feedback. The staff has been notified.');
    }
}