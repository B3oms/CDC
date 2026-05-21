<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Calamity;
use App\Models\CalamityPartner;
use App\Models\Barangay;
use App\Models\Municipality;
use App\Models\EvacuationCenter;
use App\Models\EvacuationReport;
use App\Models\HouseholdRequest;
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
        
        // Use staff layout if user is staff, otherwise use admin layout
        $view = auth()->user()->role->name === 'Staff' ? 'staff.calamity.index' : 'admin.calamity.index';
        
        return view($view, compact('calamities'));
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
        $calamity = Calamity::with(['barangays', 'evacuationReports.barangay', 'evacuationReports.evacuationCenter'])
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

        // Use staff layout if user is staff, otherwise use admin layout
        $view = auth()->user()->role->name === 'Staff' ? 'staff.calamity.show' : 'admin.calamity.show';
        
        return view($view, compact('calamity', 'rankings'));
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

    // Fetch household data for AJAX requests
    public function getHouseholds($calamityId, $barangayId)
    {
        try {
            $barangay = Barangay::findOrFail($barangayId);
            
            // Get the evacuation report for this calamity + barangay
            $report = EvacuationReport::where('calamity_id', $calamityId)
                ->where('barangay_id', $barangayId)
                ->first();
            
            // Get the household IDs that were submitted in the portal
            $submittedIds = [];
            if ($report) {
                try {
                    // Check if household_ids column exists and is accessible
                    if (isset($report->household_ids)) {
                        // Manually decode JSON since cast might not be working for existing records
                        $householdIdsData = $report->household_ids;
                        if (is_string($householdIdsData)) {
                            $submittedIds = json_decode($householdIdsData, true) ?: [];
                        } elseif (is_array($householdIdsData)) {
                            $submittedIds = $householdIdsData;
                        } else {
                            $submittedIds = [];
                        }
                    }
                } catch (\Exception $e) {
                    $submittedIds = [];
                }
            }
            
            // Fetch only the households that were actually inputted in the portal
            $households = collect();
            if (!empty($submittedIds)) {
                $households = HouseholdRequest::whereIn('id', $submittedIds)
                    ->orderBy('head_of_household')
                    ->get(['id', 'head_of_household', 'family_size', 'contact_number']);
            }

            return response()->json([
                'success' => true,
                'barangay_name' => $barangay->name,
                'total_households' => $households->count(),
                'households' => $households->map(function($household) {
                    return [
                        'head_name' => $household->head_of_household,
                        'member_count' => $household->family_size,
                        'household_code' => 'HH-' . str_pad($household->id, 4, '0', STR_PAD_LEFT),
                        'contact_number' => $household->contact_number
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch household data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete a calamity and all related data
    public function destroy($id)
    {
        try {
            $calamity = Calamity::findOrFail($id);
            
            // Delete related evacuation centers
            EvacuationCenter::where('calamity_id', $calamity->id)->delete();
            
            // Delete related evacuation reports
            EvacuationReport::where('calamity_id', $calamity->id)->delete();
            
            // Delete related calamity partners
            CalamityPartner::where('calamity_id', $calamity->id)->delete();
            
            // Delete the calamity
            $calamity->delete();
            
            return redirect()->route('admin.calamity.index')
                ->with('success', 'Calamity portal and all related data deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.calamity.index')
                ->with('error', 'Failed to delete calamity portal: ' . $e->getMessage());
        }
    }

    // Download calamity portal details as PDF
    public function downloadPDF(Request $request, $id)
    {
        try {
            $calamity = Calamity::with(['barangays', 'evacuationReports.barangay', 'evacuationReports.evacuationCenter'])
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

            // Get household data for each barangay
            $barangayHouseholds = [];
            foreach ($calamity->barangays as $barangay) {
                // Get the evacuation report for this calamity + barangay
                $report = EvacuationReport::where('calamity_id', $id)
                    ->where('barangay_id', $barangay->id)
                    ->first();

                $households = [];
                if ($report) {
                    try {
                        // Get the household IDs that were submitted in the portal
                        $submittedIds = [];
                        if (isset($report->household_ids)) {
                            $householdIdsData = $report->household_ids;
                            if (is_string($householdIdsData)) {
                                $submittedIds = json_decode($householdIdsData, true) ?: [];
                            } elseif (is_array($householdIdsData)) {
                                $submittedIds = $householdIdsData;
                            }
                        }

                        if (!empty($submittedIds)) {
                            // Get household details for submitted IDs
                            $households = \App\Models\HouseholdRequest::whereIn('id', $submittedIds)
                                ->where('barangay_id', $barangay->id)
                                ->orderBy('head_of_household')
                                ->get(['id', 'head_of_household', 'family_size', 'contact_number']);
                        }
                    } catch (\Exception $e) {
                        $households = [];
                    }
                }

                $barangayHouseholds[$barangay->id] = $households;
            }

            // Prepare data for PDF
            $pdfData = [
                'calamity' => $calamity,
                'rankings' => $rankings,
                'barangayHouseholds' => $barangayHouseholds,
                'generated_date' => now()->format('F d, Y - h:i A')
            ];

            // Get paper size and orientation from request (default to A4 landscape)
            $paperSize = $request->input('paper_size', 'A4');
            $orientation = $request->input('orientation', 'landscape');

            // Generate PDF using DomPDF
            try {
                $html = view('admin.calamity.pdf', $pdfData)->render();
                
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml($html);
                
                // Set paper size and orientation
                $dompdf->setPaper($paperSize, $orientation);
                
                // Set options for better rendering
                $options = new \Dompdf\Options();
                $options->set('defaultFont', 'Arial');
                $options->set('isRemoteEnabled', true);
                $options->set('isHtml5ParserEnabled', true);
                $options->set('isFontSubsettingEnabled', true);
                $dompdf->setOptions($options);
                
                // Render the PDF
                $dompdf->render();
                
                // Generate filename
                $filename = 'calamity-' . $calamity->name . '-' . $calamity->id . '.pdf';
                
                // Return PDF download
                return response($dompdf->output(), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                ]);
            } catch (\Exception $e) {
                // Log the error for debugging
                \Log::error('PDF generation failed: ' . $e->getMessage());
                
                // Return a simple error response
                return response()->json([
                    'error' => 'PDF generation failed: ' . $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }

        } catch (\Exception $e) {
            return redirect()->route('admin.calamity.show', $id)
                ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
}