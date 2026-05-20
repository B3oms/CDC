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
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        // Count verified beneficiaries
        $verifiedBeneficiaries = Beneficiary::where('is_verified', true)->count();

        $beneficiariesThisYear = Beneficiary::whereYear(
            'created_at',
            now()->year
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Yearly Distribution Reports (Staff Structure)
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | Monthly Distribution Statistics (Staff Structure)
        |--------------------------------------------------------------------------
        */
        $monthlyData = ReliefEvent::select(
                DB::raw('MONTH(date) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Prepare yearly trend data for charts
        $yearlyTrendLabels = $yearlyData->keys()->toArray();
        $yearlyTrendValues = $yearlyData->map(function($months) {
            return $months->sum('total');
        })->all();

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
            'yearlyTrendLabels',
            'yearlyTrendValues',

            'calamityTypes',
            'topBarangays',

            'totalInventoryItems',
            'lowStockItems',
            'expiringItems',

            'activeCalamity'
        ));
    }

    // Fetch real-time dashboard statistics
    public function getStats()
    {
        $stats = [
            'barangayCount' => Barangay::count(),
            'municipalityCount' => Municipality::count(),
            'regionCount' => Municipality::distinct('province')->count('province'),
            'totalDistributions' => ReliefEvent::count(),
            'verifiedBeneficiaries' => Beneficiary::where('is_verified', true)->count(),
            'totalInventoryItems' => Item::count(),
            'lowStockItems' => Inventory::where('quantity', '<=', DB::raw('reorder_level'))->count(),
            'expiringItems' => Item::where('expiration_date', '<=', now()->addDays(30))->count(),
            'activeStaff' => User::whereHas('role', function($q) { $q->where('name', 'Staff'); })->count(),
            'pendingLocations' => \App\Models\Municipality::pending()->count(),
            'activePartners' => User::whereHas('role', function($q) { $q->where('name', 'Barangay Partner'); })->where('status', 'active')->count(),
            'lastUpdated' => now()->format('M d, Y H:i:s')
        ];

        return response()->json($stats);
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    // Update user profile
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birthdate' => 'nullable|date',
            'position' => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'birthdate' => $request->birthdate,
            'position' => $request->position,
            'organization' => $request->organization,
        ]);

        return redirect()->route('admin.profile');
    }

    // Update user password
    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('admin.profile')
            ->with('success', 'Password updated successfully!');
    }

    /**
     * Display the user's profile
     */
    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }
}