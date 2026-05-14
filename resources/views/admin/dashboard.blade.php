@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')

<div class="dash-header">
    <h1>Welcome back, {{ auth()->user()->first_name }}!</h1>

    <a href="{{ route('admin.calamity.index') }}"
    class="calamity-meter {{ $activeCalamity ? 'active' : 'none' }}"
    style="text-decoration:none;">
    <div class="cal-label">Calamity Meter ↗</div>
    @if($activeCalamity)
    <div class="cal-name">{{ $activeCalamity->name }}</div>
    <span class="cal-badge">Active</span>
    @else
    <div class="cal-name">View All Portals</div>
    @endif
</a>
</div>

@if($activeCalamity)
<div class="alert alert-info" style="margin-bottom:1.5rem;">
    <i class="fas fa-info-circle"></i>
    <strong>Active Calamity:</strong> {{ $activeCalamity->name }} 
    <span class="badge-intensity {{ strtolower($activeCalamity->intensity) }}">{{ $activeCalamity->intensity }}</span>
</div>
@endif

{{-- Enhanced Statistics Row --}}
<div class="stats-row" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-num" id="barangayCount">{{ $barangayCount ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-map"></i> Total Barangays
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num" id="municipalityCount">{{ $municipalityCount ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-city"></i> Municipalities
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num" id="totalDistributions">{{ $totalDistributions ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-boxes"></i> Total Distributions
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num" id="verifiedBeneficiaries">{{ $verifiedBeneficiaries ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-check-circle"></i> Verified Beneficiaries
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#dc3545;" id="lowStockItems">{{ $lowStockItems ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-exclamation-triangle"></i> Low Stock Alerts
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#ffc107;" id="expiringItems">{{ $expiringItems ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-clock"></i> Expiring Items (30 days)
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#ef9f27;" id="pendingLocations">{{ \App\Models\Municipality::pending()->count() }}</div>
        <div class="stat-label">
            <i class="fas fa-clock"></i> Pending Locations
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#888780;" id="activeStaff">{{ \App\Models\User::whereHas('role', function($q) { $q->where('name', 'Staff'); })->count() }}</div>
        <div class="stat-label">
            <i class="fas fa-users"></i> Active Staff
        </div>
    </div>
</div>

<div class="dash-grid">
    {{-- LEFT: Charts --}}
    <div class="yearly-col">
        <div class="chart-card">
            <div class="chart-title">MONTHLY TREND</div>
            <canvas id="chart-monthly" height="100" style="height: 100px; width: 100%;"></canvas>
            <button onclick="exportChartToPDF('chart-monthly', 'monthly-trend')" class="pdf-export-btn">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
        </div>

        {{-- Debug: Show yearly data structure --}}
{{-- {{ dump($yearlyData) }} --}}

@forelse($yearlyData as $year => $months)
{{-- Yearly Trend Chart --}}
        <div class="chart-card">
            <div class="chart-title">YEARLY TREND</div>
            <canvas id="chart-yearly-trend"
                data-labels="{{ json_encode($yearlyTrendLabels) }}"
                data-values="{{ json_encode($yearlyTrendValues) }}"
                height="80" style="height: 80px; width: 100%;">
            </canvas>
            <div class="chart-actions">
                <button onclick="exportChartToPDF('chart-yearly-trend', 'yearly-trend')" class="pdf-export-btn">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
                <a href="{{ route('admin.relief.index') }}" class="view-link">View →</a>
            </div>
        </div>
        @empty
        <div class="chart-card">
            <div class="chart-title">No relief data yet</div>
            <p style="font-size:12px;color:#888;margin-top:8px;">
                Create relief events to see charts.
            </p>
        </div>
        @endforelse
    </div>

    {{-- RIGHT --}}
    <div class="right-col">

        {{-- Staff --}}
        <div class="section-card">
            <h3>Staff</h3>
            <div class="staff-row">
                @forelse($staff as $s)
                <div class="staff-item">
                    <div class="avatar">
                        {{ strtoupper(substr($s->first_name,0,1).substr($s->last_name,0,1)) }}
                    </div>
                    <div>
                        <div class="staff-name">{{ $s->first_name }} {{ $s->last_name }}</div>
                        <div class="staff-role">{{ $s->role->name }}</div>
                    </div>
                </div>
                @empty
                <p style="font-size:12px;color:#888;">No staff found.</p>
                @endforelse
            </div>
            <a href="{{ route('admin.staff.index') }}" class="see-all">See all →</a>
        </div>

        {{-- Upcoming & Ongoing Distributions --}}
        @if($upcomingEvents->count())
        <div class="section-card">
            <h3>Upcoming & Ongoing Relief Events</h3>
            <table class="dist-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Barangay</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcomingEvents as $event)
                    <tr>
                        <td>
                            <a href="{{ route('admin.relief.show', $event->id) }}"
                                style="color:#185fa5;text-decoration:none;">
                                {{ $event->name }}
                            </a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</td>
                        <td>
                            @foreach($event->eventBarangays as $eb)
                                {{ $eb->barangay->name }}@if(!$loop->last), @endif
                            @endforeach
                        </td>
                        <td>
                            <span class="relief-status-badge {{ strtolower($event->status) }}">
                                {{ $event->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <a href="{{ route('admin.relief.index') }}" class="see-all">See all →</a>
        </div>
        @endif

        {{-- Completed Distributions --}}
        @if($completedEvents->count())
        <div class="section-card">
            <h3>Completed Relief Events</h3>
            <table class="dist-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Barangay</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($completedEvents->take(5) as $event)
                    <tr>
                        <td>
                            <a href="{{ route('admin.relief.show', $event->id) }}"
                                style="color:#185fa5;text-decoration:none;">
                                {{ $event->name }}
                            </a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</td>
                        <td>
                            @foreach($event->eventBarangays as $eb)
                                {{ $eb->barangay->name }}@if(!$loop->last), @endif
                            @endforeach
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <a href="{{ route('admin.relief.index') }}" class="see-all">See all →</a>
        </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Chart
    const monthlyCtx = document.getElementById('chart-monthly');
    if (monthlyCtx) {
        // Process actual database data
        const monthlyRaw = @json($monthlyData);
        const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        const monthlyValues = monthNames.map((_, i) => {
            const found = monthlyRaw.find(m => m.month === i + 1);
            return found ? found.total : 0;
        });

        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthNames,
                datasets: [{
                    label: 'Relief Events',
                    data: monthlyValues,
                    backgroundColor: 'rgba(26, 61, 31, 0.7)',
                    borderColor: 'rgba(26, 61, 31, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Yearly Trend Chart
    const yearlyTrendCanvas = document.getElementById('chart-yearly-trend');
    if (yearlyTrendCanvas && yearlyTrendCanvas.dataset.labels && yearlyTrendCanvas.dataset.values) {
        const yearLabels = JSON.parse(yearlyTrendCanvas.dataset.labels);
        const yearValues = JSON.parse(yearlyTrendCanvas.dataset.values);
        
        new Chart(yearlyTrendCanvas, {
            type: 'line',
            data: {
                labels: yearLabels,
                datasets: [{
                    label: 'Relief Events',
                    data: yearValues,
                    borderColor: '#1a3d1f',
                    backgroundColor: 'rgba(26, 61, 31, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});

// PDF Export Function
function exportChartToPDF(chartId, filename) {
    const { jsPDF } = window.jspdf;
    const chartElement = document.getElementById(chartId);
    
    // Get chart title for PDF header
    const chartTitle = chartElement.closest('.chart-card').querySelector('.chart-title').textContent;
    
    html2canvas(chartElement, {
        backgroundColor: '#ffffff',
        scale: 3,
        useCORS: true,
        allowTaint: true
    }).then(canvas => {
        const imgData = canvas.toDataURL('image/png', 1.0);
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });
        
        // PDF dimensions
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();
        const margin = 20;
        
        // Calculate optimal image dimensions
        const maxWidth = pageWidth - (margin * 2);
        const maxHeight = pageHeight - (margin * 3); // Leave space for title
        
        // Calculate image dimensions maintaining aspect ratio
        const imgWidth = maxWidth;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        
        // Adjust if image is too tall
        let finalHeight = imgHeight;
        if (imgHeight > maxHeight) {
            finalHeight = maxHeight;
            const finalWidth = (canvas.width * finalHeight) / canvas.height;
            // Recenter if width changed
            const xPosition = (pageWidth - finalWidth) / 2;
            
            // Add title
            pdf.setFontSize(16);
            pdf.setFont('helvetica', 'bold');
            pdf.text(chartTitle, pageWidth / 2, margin, { align: 'center' });
            
            // Add chart image
            pdf.addImage(imgData, 'PNG', xPosition, margin + 15, finalWidth, finalHeight);
        } else {
            // Add title
            pdf.setFontSize(16);
            pdf.setFont('helvetica', 'bold');
            pdf.text(chartTitle, pageWidth / 2, margin, { align: 'center' });
            
            // Add chart image centered
            const xPosition = (pageWidth - imgWidth) / 2;
            pdf.addImage(imgData, 'PNG', xPosition, margin + 15, imgWidth, finalHeight);
        }
        
        // Add footer with timestamp
        pdf.setFontSize(10);
        pdf.setFont('helvetica', 'normal');
        const timestamp = new Date().toLocaleString();
        pdf.text(`Generated: ${timestamp}`, pageWidth / 2, pageHeight - 10, { align: 'center' });
        
        // Save PDF with professional filename
        const cleanFilename = filename.replace(/[^a-z0-9]/gi, '_').toLowerCase();
        pdf.save(`${cleanFilename}_report_${new Date().toISOString().split('T')[0]}.pdf`);
    });
}
</script>
@endpush

@push('styles')
<style>
/* Chart Grid */
.dash-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 1.5rem;
}

/* PDF Export Buttons */
.pdf-export-btn {
    background: #dc3545;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    margin-top: 8px;
    transition: background-color 0.3s;
}

.pdf-export-btn:hover {
    background: #c82333;
}

.pdf-export-btn i {
    margin-right: 4px;
}

.chart-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
}

.view-link {
    color: #1a3d1f;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
}

.view-link:hover {
    text-decoration: underline;
}

.yearly-col {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.chart-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.chart-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #2c2c2a;
    margin: 0 0 0.75rem 0;
}

.chart-card a {
    color: #1a3d1f;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 500;
    margin-top: 0.5rem;
    display: inline-block;
}

.chart-card a:hover {
    text-decoration: underline;
}

.chart-wrapper {
    background: white;
    border-radius: 1rem;
    padding: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
    height: 280px;
    display: flex;
    flex-direction: column;
}

.chart-wrapper:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.chart-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f3f4f6;
    flex-shrink: 0;
}

.chart-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 0.25rem 0;
    letter-spacing: -0.025em;
}

.chart-subtitle {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 400;
    margin: 0;
}

.chart-body {
    position: relative;
    flex: 1;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fafafa;
    border-radius: 0.5rem;
    overflow: hidden;
}

.chart-body canvas {
    max-width: 100%;
    max-height: 100%;
    position: relative;
    z-index: 2;
}

.chart-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: #9ca3af;
    z-index: 1;
}

.chart-loading i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.charts-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.chart-wrapper.half .chart-body {
    height: 250px;
}

.year-chart {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #f3f4f6;
}

.year-chart:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.year-chart h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
    margin: 0 0 1rem 0;
}

.year-chart canvas {
    height: 120px !important;
}

.no-data {
    text-align: center;
    padding: 3rem 1rem;
    color: #9ca3af;
}

.no-data i {
    font-size: 3rem;
    margin-bottom: 1rem;
    display: block;
}

.no-data p {
    font-size: 1.125rem;
    font-weight: 500;
    margin: 0 0 0.5rem 0;
    color: #6b7280;
}

.no-data small {
    font-size: 0.875rem;
    color: #9ca3af;
}

.more-data {
    text-align: center;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #f3f4f6;
}

.view-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #f3f4f6;
    color: #374151;
    text-decoration: none;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.view-more-btn:hover {
    background: #e5e7eb;
    color: #1f2937;
    transform: translateY(-1px);
}

.view-more-btn i {
    font-size: 1rem;
}

/* Chart.js Custom Styling */
canvas {
    max-height: 100%;
}

/* Responsive Design */
@media (max-width: 1023px) {
    .charts-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .chart-wrapper {
        height: 250px;
    }
}

@media (max-width: 768px) {
    .chart-wrapper {
        height: 220px;
        padding: 0.75rem;
    }
}

/* Pulse Animation */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.stat-num {
    transition: all 0.3s ease;
}

.stat-num.updating {
    color: #10b981;
    transform: scale(1.1);
}
</style>
@endpush

<script>
let refreshInterval;
let isUpdating = false;

function updateDashboardStats() {
    if (isUpdating) return;
    isUpdating = true;
    
    fetch('{{ route("admin.dashboard.stats") }}')
        .then(response => response.json())
        .then(data => {
            // Update statistics with animation
            updateStatWithAnimation('barangayCount', data.barangayCount);
            updateStatWithAnimation('municipalityCount', data.municipalityCount);
            updateStatWithAnimation('totalDistributions', data.totalDistributions);
            updateStatWithAnimation('verifiedBeneficiaries', data.verifiedBeneficiaries);
            updateStatWithAnimation('lowStockItems', data.lowStockItems);
            updateStatWithAnimation('expiringItems', data.expiringItems);
            updateStatWithAnimation('activeStaff', data.activeStaff);
            updateStatWithAnimation('pendingLocations', data.pendingLocations);
            
            // Update last updated time
            document.getElementById('lastUpdated').textContent = data.lastUpdated;
            
            // Show update indicator
            showUpdateIndicator();
        })
        .catch(error => {
            console.error('Error fetching dashboard stats:', error);
        })
        .finally(() => {
            isUpdating = false;
        });
}

function updateStatWithAnimation(elementId, newValue) {
    const element = document.getElementById(elementId);
    const oldValue = parseInt(element.textContent);
    
    if (oldValue !== newValue) {
        element.classList.add('updating');
        
        // Animate number change
        animateValue(element, oldValue, newValue, 500);
        
        setTimeout(() => {
            element.classList.remove('updating');
        }, 500);
    }
}

function animateValue(element, start, end, duration) {
    const range = end - start;
    const increment = range > 0 ? 1 : -1;
    const stepTime = Math.abs(Math.floor(duration / range));
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        element.textContent = current;
        
        if (current === end) {
            clearInterval(timer);
        }
    }, stepTime);
}

function showUpdateIndicator() {
    const dot = document.querySelector('.realtime-dot');
    dot.style.background = '#10b981';
    dot.style.animation = 'none';
    
    setTimeout(() => {
        dot.style.animation = 'pulse 2s infinite';
    }, 100);
}

// Start real-time updates
function startRealTimeUpdates() {
    // Initial load
    updateDashboardStats();
    
    // Refresh every 30 seconds
    refreshInterval = setInterval(updateDashboardStats, 30000);
}

// Stop real-time updates when page is not visible
function handleVisibilityChange() {
    if (document.hidden) {
        clearInterval(refreshInterval);
    } else {
        startRealTimeUpdates();
    }
}

// Initialize real-time functionality
document.addEventListener('DOMContentLoaded', function() {
    startRealTimeUpdates();
    
    // Handle page visibility
    document.addEventListener('visibilitychange', handleVisibilityChange);
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        clearInterval(refreshInterval);
    });
});

// Manual refresh button (optional)
document.addEventListener('keydown', function(e) {
    // Press 'R' key to manually refresh
    if (e.key === 'r' && !e.ctrlKey && !e.metaKey && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
        e.preventDefault();
        updateDashboardStats();
    }
});
</script>

@push('styles')
<style>
.dash-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.dash-header h1 {
    color: #2c2c2a;
    font-family: 'Segoe UI', sans-serif;
    font-size: 1.75rem;
    font-weight: 600;
    margin: 0;
}

/* Smaller calamity meter */
.calamity-meter {
    background: #faeeda;
    border: 1px solid #ef9f27;
    border-radius: 6px;
    padding: 8px 12px;
    text-align: right;
    min-width: 140px;
    font-size: 0.85rem;
}

.calamity-meter .cal-label {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #b8860b;
    margin-bottom: 2px;
    font-weight: 500;
}

.calamity-meter .cal-name {
    font-size: 11px;
    font-weight: 600;
    color: #633806;
    line-height: 1.2;
}

.calamity-meter .cal-badge {
    display: inline-block;
    background: #e24b4a;
    color: #fff;
    font-size: 8px;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 500;
    margin-top: 2px;
}

.calamity-meter.none {
    background: #eaf3de;
    border-color: #639922;
}

.calamity-meter.none .cal-label { color: #3b6d11; }
.calamity-meter.none .cal-name  { color: #27500a; }
</style>
@endpush