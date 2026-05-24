<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ReliefEvent;
use App\Models\Calamity;
use App\Models\Barangay;
use App\Models\Municipality;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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

        // Prepare yearly trend data for charts
        $yearlyTrendLabels = $yearlyData->keys()->toArray();
        $yearlyTrendValues = $yearlyData->map(function($months) {
            return $months->sum('total');
        })->all();

        $activeCalamity = Calamity::where('status', 'Open')->latest()->first();

        return view('staff.dashboard', compact(
            'barangayCount', 'municipalityCount', 'regionCount',
            'staff', 'upcomingEvents', 'completedEvents',
            'yearlyData', 'monthlyData', 'yearlyTrendLabels', 'yearlyTrendValues', 'activeCalamity'
        ));
    }

    /**
     * Get staff notifications
     */
    public function getNotifications()
    {
        $notifications = \App\Services\NotificationService::getRecentNotifications(auth()->id(), 10);
        $unreadCount   = \App\Services\NotificationService::getUnreadCount(auth()->id());

        return response()->json([
            'notifications' => $notifications->map(function ($n) {
                return [
                    'id'    => $n->id,
                    'title' => $n->title,
                    'text'  => $n->message,
                    'type'  => $n->type,
                    'icon'  => $n->icon,
                    'color' => $n->color,
                    'unread'=> !$n->read,
                    'time'  => $n->created_at->diffForHumans(),
                    'url'   => $this->resolveStaffUrl($n->type),
                ];
            }),
            'unread_count' => $unreadCount,
        ]);
    }

    private function resolveStaffUrl(string $type): string
    {
        try {
            return match($type) {
                'beneficiary_addition'      => route('staff.beneficiaries.index'),
                'event_creation'            => route('staff.relief.index'),
                'recommendation_submitted'  => route('staff.recommended.index'),
                'location_request_approved',
                'location_request_rejected' => route('staff.location-requests.index'),
                default                     => '#',
            };
        } catch (\Exception $e) {
            return '#';
        }
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($notificationId)
    {
        $notification = \App\Models\Notification::where('user_id', auth()->id())
            ->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
            'unread_count' => \App\Services\NotificationService::getUnreadCount(auth()->id()),
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        \App\Services\NotificationService::markAllAsRead(auth()->id());

        return response()->json(['success' => true, 'unread_count' => 0]);
    }

    /**
     * Export chart as PDF
     */
    public function exportChartPdf(Request $request, $type)
    {
        $paperSize = $request->query('paper_size', 'A4');
        $orientation = $request->query('orientation', 'portrait');
        
        // Get chart data based on type
        if ($type === 'monthly') {
            $chartData = $this->getMonthlyChartData();
            $chartTitle = 'Monthly Relief Trend Analysis';
            $chartInterpretation = $this->getMonthlyInterpretation($chartData);
        } elseif ($type === 'yearly') {
            $chartData = $this->getYearlyChartData();
            $chartTitle = 'Yearly Relief Trend Analysis';
            $chartInterpretation = $this->getYearlyInterpretation($chartData);
        } else {
            return response()->json(['error' => 'Invalid chart type'], 400);
        }
        
        // Generate PDF
        $pdf = $this->generateChartPdf($chartTitle, $chartData, $chartInterpretation, $paperSize, $orientation);
        
        return $pdf;
    }
    
    private function getMonthlyChartData()
    {
        $monthlyData = ReliefEvent::select(
                DB::raw('MONTH(date) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $data = [];
        
        foreach ($months as $index => $month) {
            $data[$month] = $monthlyData->where('month', $index + 1)->first()?->total ?? 0;
        }
        
        return [
            'labels' => array_keys($data),
            'values' => array_values($data)
        ];
    }
    
    private function getYearlyChartData()
    {
        $yearlyData = ReliefEvent::select(
                DB::raw('YEAR(date) as year'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('date', '>=', now()->year - 2)
            ->groupBy('year')
            ->orderBy('year')
            ->get();
            
        $data = [];
        foreach ($yearlyData as $year) {
            $data[$year->year] = $year->total;
        }
        
        return [
            'labels' => array_keys($data),
            'values' => array_values($data)
        ];
    }
    
    private function getMonthlyInterpretation($chartData)
    {
        $totalEvents = array_sum($chartData['values']);
        $maxEvents = max($chartData['values']);
        $maxMonth = $chartData['labels'][array_search($maxEvents, $chartData['values'])];
        
        return [
            'summary' => "Total relief events this year: {$totalEvents}",
            'peak' => "Peak activity in {$maxMonth} with {$maxEvents} events",
            'trend' => $this->analyzeTrend($chartData['values']),
            'recommendations' => [
                'Focus resources during peak months',
                'Prepare contingency plans for low activity periods',
                'Monitor patterns for better resource allocation'
            ]
        ];
    }
    
    private function getYearlyInterpretation($chartData)
    {
        $totalEvents = array_sum($chartData['values']);
        $avgEvents = count($chartData['values']) > 0 ? $totalEvents / count($chartData['values']) : 0;
        
        return [
            'summary' => "Total relief events in analyzed period: {$totalEvents}",
            'average' => "Average events per year: " . round($avgEvents, 1),
            'trend' => $this->analyzeTrend($chartData['values']),
            'recommendations' => [
                'Maintain consistent disaster preparedness',
                'Strengthen response capabilities',
                'Develop long-term relief strategies'
            ]
        ];
    }
    
    private function analyzeTrend($values)
    {
        if (count($values) < 2) {
            return 'Insufficient data for trend analysis';
        }
        
        $first = $values[0];
        $last = end($values);
        
        if ($last > $first * 1.2) {
            return 'Increasing trend - relief activities are growing';
        } elseif ($last < $first * 0.8) {
            return 'Decreasing trend - relief activities are declining';
        } else {
            return 'Stable trend - relief activities remain consistent';
        }
    }
    
    private function generateChartPdf($title, $chartData, $interpretation, $paperSize, $orientation)
    {
        // Create HTML content for PDF
        $html = view('staff.charts.pdf', [
            'title' => $title,
            'chartData' => $chartData,
            'interpretation' => $interpretation,
            'paperSize' => $paperSize,
            'orientation' => $orientation
        ])->render();
        
        // Generate PDF using DomPDF
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
        $filename = str_replace(' ', '_', $title) . '.pdf';
        
        // Return PDF download
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}