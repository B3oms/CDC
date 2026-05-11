<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ReliefEvent;
use App\Models\Calamity;
use App\Models\Barangay;
use App\Models\Municipality;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $barangayCount     = Barangay::count();
        $municipalityCount = Municipality::count();
        $regionCount       = Municipality::distinct('province')->count('province');

        $staff = User::with('role')
            ->whereHas('role', fn($q) => $q->where('name', 'Staff'))
            ->limit(3)->get();

        $upcomingEvents = ReliefEvent::with(['eventBarangays.barangay'])
            ->whereIn('status', ['Upcoming', 'Ongoing'])
            ->orderBy('date')
            ->get();

        $completedEvents = ReliefEvent::with(['eventBarangays.barangay'])
            ->where('status', 'Done')
            ->latest('date')
            ->get();

        $yearlyData = ReliefEvent::select(
                DB::raw('YEAR(date) as year'),
                DB::raw('MONTH(date) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('date', '>=', now()->year - 2)
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get()
            ->groupBy('year');

        $monthlyData = ReliefEvent::select(
                DB::raw('MONTH(date) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $activeCalamity = Calamity::where('status', 'Open')->latest()->first();

        return view('staff.dashboard', compact(
            'barangayCount', 'municipalityCount', 'regionCount',
            'staff', 'upcomingEvents', 'completedEvents',
            'yearlyData', 'monthlyData', 'activeCalamity'
        ));
    }
}