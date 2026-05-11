<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\ReliefEvent;
use App\Models\Calamity;
use App\Models\Barangay;
use App\Models\Municipality;
use App\Models\Item;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Basic Statistics
        |--------------------------------------------------------------------------
        */
        $barangayCount = Barangay::count();
        $municipalityCount = Municipality::count();
        $regionCount = Municipality::distinct('province')->count('province');

        /*
        |--------------------------------------------------------------------------
        | Staff Statistics
        |--------------------------------------------------------------------------
        | Assuming role_id = 2 is Staff
        */
        $staff = User::where('role_id', 2)
            ->latest()
            ->limit(3)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Relief Event Statistics
        |--------------------------------------------------------------------------
        */
        $totalDistributions = ReliefEvent::count();

        $upcomingEvents = ReliefEvent::with([
                'eventBarangays.barangay',
                'eventBarangays.municipality'
            ])
            ->whereIn('status', ['Upcoming', 'Ongoing'])
            ->orderBy('date', 'asc')
            ->take(5)
            ->get();

        $completedEvents = ReliefEvent::with([
                'eventBarangays.barangay',
                'eventBarangays.municipality'
            ])
            ->where('status', 'Done')
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Beneficiary Statistics
        |--------------------------------------------------------------------------
        */
        $totalBeneficiaries = Beneficiary::count();

        // Count verified beneficiaries from beneficiary_verifications
        $verifiedBeneficiaries = DB::table('beneficiary_verifications')
            ->distinct('beneficiary_id')
            ->count('beneficiary_id');

        $beneficiariesThisYear = Beneficiary::whereYear(
            'created_at',
            now()->year
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Yearly Distribution Reports
        |--------------------------------------------------------------------------
        */
        $yearlyData = ReliefEvent::selectRaw('
                YEAR(date) as year,
                COUNT(*) as total_distributions,
                SUM(
                    CASE
                        WHEN status = "Done"
                        THEN 1
                        ELSE 0
                    END
                ) as completed_distributions
            ')
            ->whereYear('date', '>=', now()->year - 5)
            ->groupByRaw('YEAR(date)')
            ->orderBy('year', 'asc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Monthly Distribution Statistics
        |--------------------------------------------------------------------------
        */
        $monthlyData = ReliefEvent::selectRaw('
                MONTHNAME(date) as month_name,
                MONTH(date) as month,
                COUNT(*) as total,
                SUM(
                    CASE
                        WHEN status = "Done"
                        THEN 1
                        ELSE 0
                    END
                ) as completed
            ')
            ->whereYear('date', now()->year)
            ->groupByRaw('MONTH(date), MONTHNAME(date)')
            ->orderByRaw('MONTH(date) ASC')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Calamity Types Handled
        |--------------------------------------------------------------------------
        */
        $calamityTypes = ReliefEvent::join(
                'calamities',
                'relief_events.calamity_id',
                '=',
                'calamities.id'
            )
            ->selectRaw('
                calamities.name as calamity_name,
                COUNT(*) as count
            ')
            ->groupBy('calamities.name')
            ->orderByDesc('count')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Top Priority Barangays
        |--------------------------------------------------------------------------
        */
        $topBarangays = DB::table('relief_events')
            ->join(
                'relief_event_barangays',
                'relief_events.id',
                '=',
                'relief_event_barangays.relief_event_id'
            )
            ->join(
                'barangays',
                'relief_event_barangays.barangay_id',
                '=',
                'barangays.id'
            )
            ->join(
                'municipalities',
                'barangays.municipality_id',
                '=',
                'municipalities.id'
            )
            ->selectRaw('
                barangays.id,
                barangays.name as barangay_name,
                municipalities.name as municipality_name,
                COUNT(relief_events.id) as distribution_count,
                MAX(relief_events.created_at) as last_distribution
            ')
            ->where('relief_events.status', 'Done')
            ->groupBy(
                'barangays.id',
                'barangays.name',
                'municipalities.name'
            )
            ->orderByDesc('distribution_count')
            ->take(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Inventory Statistics
        |--------------------------------------------------------------------------
        */
        $totalInventoryItems = Item::count();

        // inventory.quantity
        $lowStockItems = Inventory::where('quantity', '<=', 10)->count();

        // inventory.expiration_date
        $expiringItems = Inventory::whereNotNull('expiration_date')
            ->whereDate(
                'expiration_date',
                '<=',
                now()->addDays(30)
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Active Calamity
        |--------------------------------------------------------------------------
        */
        $activeCalamity = Calamity::where('status', 'Open')
            ->latest()
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */
        return view('admin.dashboard', compact(
            'barangayCount',
            'municipalityCount',
            'regionCount',

            'staff',

            'totalDistributions',
            'upcomingEvents',
            'completedEvents',

            'totalBeneficiaries',
            'verifiedBeneficiaries',
            'beneficiariesThisYear',

            'yearlyData',
            'monthlyData',

            'calamityTypes',
            'topBarangays',

            'totalInventoryItems',
            'lowStockItems',
            'expiringItems',

            'activeCalamity'
        ));
    }
}