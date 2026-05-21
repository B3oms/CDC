<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $beneficiary = $user->beneficiary;
        
        if (!$beneficiary) {
            abort(403, 'Beneficiary profile not found');
        }

        // Get relief events where this beneficiary participated
        $reliefEvents = \App\Models\ReliefEventBeneficiary::where('beneficiary_id', $beneficiary->id)
            ->with(['reliefEvent.calamity', 'reliefEvent.distributedItems.item'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get distributions for this beneficiary
        $distributions = \App\Models\Distribution::where('beneficiary_id', $beneficiary->id)
            ->with(['reliefEvent', 'item'])
            ->orderBy('distributed_at', 'desc')
            ->get();

        // Calculate statistics
        $totalEvents = $reliefEvents->count();
        $totalItemsReceived = $distributions->sum('quantity');
        $totalValue = $distributions->sum(function($distribution) {
            return $distribution->quantity * ($distribution->item->estimated_value ?? 0);
        });

        // Group relief events by year
        $eventsByYear = $reliefEvents->groupBy(function($event) {
            return \Carbon\Carbon::parse($event->reliefEvent->date)->format('Y');
        });

        return view('beneficiary.dashboard', compact(
            'beneficiary',
            'reliefEvents',
            'distributions',
            'totalEvents',
            'totalItemsReceived',
            'totalValue',
            'eventsByYear'
        ));
    }

    public function profile()
    {
        $user = Auth::user();
        $beneficiary = $user->beneficiary;
        
        if (!$beneficiary) {
            abort(403, 'Beneficiary profile not found');
        }

        return view('beneficiary.profile', compact('beneficiary'));
    }

    public function reliefHistory()
    {
        $user = Auth::user();
        $beneficiary = $user->beneficiary;
        
        if (!$beneficiary) {
            abort(403, 'Beneficiary profile not found');
        }

        // Get detailed relief history
        $reliefHistory = \App\Models\ReliefEventBeneficiary::where('beneficiary_id', $beneficiary->id)
            ->with(['reliefEvent.calamity', 'reliefEvent.distributedItems.item'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('beneficiary.relief-history', compact('reliefHistory', 'beneficiary'));
    }
}
