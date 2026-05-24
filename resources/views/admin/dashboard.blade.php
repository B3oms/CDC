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
            <div class="cal-name">View All</div>
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

{{-- Stats Row --}}
<div class="stats-row" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-num" id="barangayCount">{{ $barangayCount ?? 0 }}</div>
        <div class="stat-label"><i class="fas fa-map"></i> Total Barangays</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" id="municipalityCount">{{ $municipalityCount ?? 0 }}</div>
        <div class="stat-label"><i class="fas fa-city"></i> Municipalities</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" id="totalDistributions">{{ $totalDistributions ?? 0 }}</div>
        <div class="stat-label"><i class="fas fa-boxes"></i> Total Distributions</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" id="verifiedBeneficiaries">{{ $verifiedBeneficiaries ?? 0 }}</div>
        <div class="stat-label"><i class="fas fa-check-circle"></i> Verified Beneficiaries</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#dc3545;" id="lowStockItems">{{ $lowStockCount ?? 0 }}</div>
        <div class="stat-label"><i class="fas fa-exclamation-triangle"></i> Low Stock Alerts</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#ffc107;" id="expiringItems">{{ $expiringCount ?? 0 }}</div>
        <div class="stat-label"><i class="fas fa-clock"></i> Expiring Items (30 days)</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#ef9f27;" id="pendingLocations">{{ \App\Models\Municipality::pending()->count() }}</div>
        <div class="stat-label"><i class="fas fa-clock"></i> Pending Locations</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#888780;" id="activeStaff">{{ \App\Models\User::whereHas('role', function($q) { $q->where('name', 'Staff'); })->count() }}</div>
        <div class="stat-label"><i class="fas fa-users"></i> Active Staff</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#1a6b2a;" id="activePartners">{{ \App\Models\User::whereHas('role', function($q) { $q->where('name', 'Barangay Partner'); })->where('status', 'active')->count() }}</div>
        <div class="stat-label"><i class="fas fa-handshake"></i> Active Barangay Partners</div>
    </div>
</div>

{{-- Charts Row --}}
<div class="charts-row">
    <div class="chart-card">
        <div class="chart-title">MONTHLY TREND</div>
        <canvas id="chart-monthly" style="width:100%; max-height:220px;"></canvas>
        <div class="chart-actions">
            <x-pdf-export-dropdown
                dropdown-id="pdfOptions-monthly"
                paper-size-id="paperSize-monthly"
                orientation-id="orientation-monthly"
                export-onclick="exportChartPdf('monthly')" />
        </div>
    </div>

    @forelse($yearlyData as $year => $months)
    <div class="chart-card">
        <div class="chart-title">YEARLY TREND</div>
        <canvas id="chart-yearly-trend"
            data-labels="{{ json_encode($yearlyTrendLabels) }}"
            data-values="{{ json_encode($yearlyTrendValues) }}"
            style="width:100%; max-height:220px;">
        </canvas>
        <div class="chart-actions">
            <x-pdf-export-dropdown
                dropdown-id="pdfOptions-yearly"
                paper-size-id="paperSize-yearly"
                orientation-id="orientation-yearly"
                export-onclick="exportChartPdf('yearly')" />
        </div>
    </div>
    @empty
    <div class="chart-card">
        <div class="chart-title">No relief data yet</div>
        <p style="font-size:12px;color:#888;margin-top:8px;">Create relief events to see charts.</p>
    </div>
    @endforelse
</div>

{{-- Bottom Section: Events --}}
<div class="bottom-grid">

    @if(isset($upcomingEvents) && $upcomingEvents->count())
    <div class="db-section-card">
        <h3 class="db-section-title">Upcoming &amp; Ongoing Relief Events</h3>
        <div class="db-table-scroll">
            <table class="db-table">
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
                            <a href="{{ route('admin.relief.show', $event->id) }}" class="db-link">
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
        </div>
    </div>
    @endif

    @if(isset($completedEvents) && $completedEvents->count())
    <div class="db-section-card">
        <h3 class="db-section-title">Completed Relief Events</h3>
        <div class="db-table-scroll">
            <table class="db-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Barangay</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($completedEvents as $event)
                    <tr>
                        <td>
                            <a href="{{ route('admin.relief.show', $event->id) }}" class="db-link">
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
        </div>
    </div>
    @endif

</div>

@endsection

@push('styles')
@include('partials.dashboard-styles')
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Monthly Chart
    const monthlyCtx = document.getElementById('chart-monthly');
    if (monthlyCtx) {
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
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { grid: { display: false } }
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
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Real-time stats
    startRealTimeUpdates();
    document.addEventListener('visibilitychange', handleVisibilityChange);
    window.addEventListener('beforeunload', () => clearInterval(refreshInterval));
});

// PDF Export
function exportChartPdf(chartType) {
    const paperSize = document.getElementById(`paperSize-${chartType}`).value;
    const orientation = document.getElementById(`orientation-${chartType}`).value;
    const url = `{{ route('admin.dashboard.chart.pdf', ['type' => '__type__']) }}`.replace('__type__', chartType);
    
    // Create a hidden form to submit for download
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = url;
    form.style.display = 'none';
    
    const paperSizeInput = document.createElement('input');
    paperSizeInput.type = 'hidden';
    paperSizeInput.name = 'paper_size';
    paperSizeInput.value = paperSize;
    form.appendChild(paperSizeInput);
    
    const orientationInput = document.createElement('input');
    orientationInput.type = 'hidden';
    orientationInput.name = 'orientation';
    orientationInput.value = orientation;
    form.appendChild(orientationInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Close dropdown after submission
    closePdfDropdown(`pdfOptions-${chartType}`);
}

let refreshInterval;
let isUpdating = false;

function updateDashboardStats() {
    if (isUpdating) return;
    isUpdating = true;

    fetch('{{ route("admin.dashboard.stats") }}')
        .then(r => r.json())
        .then(data => {
            ['barangayCount','municipalityCount','totalDistributions','verifiedBeneficiaries',
             'lowStockItems','expiringItems','activeStaff','pendingLocations','activePartners']
            .forEach(id => updateStatWithAnimation(id, data[id]));
        })
        .catch(err => console.error('Stats fetch error:', err))
        .finally(() => { isUpdating = false; });
}

function updateStatWithAnimation(id, newValue) {
    const el = document.getElementById(id);
    if (!el) return;
    const oldValue = parseInt(el.textContent) || 0;
    if (oldValue === newValue) return;
    el.classList.add('updating');
    animateValue(el, oldValue, newValue, 500);
    setTimeout(() => el.classList.remove('updating'), 500);
}

function animateValue(el, start, end, duration) {
    const range = end - start;
    if (range === 0) return;
    const step = range > 0 ? 1 : -1;
    const stepTime = Math.max(1, Math.abs(Math.floor(duration / range)));
    let current = start;
    const timer = setInterval(() => {
        current += step;
        el.textContent = current;
        if (current === end) clearInterval(timer);
    }, stepTime);
}

function startRealTimeUpdates() {
    updateDashboardStats();
    refreshInterval = setInterval(updateDashboardStats, 30000);
}

function handleVisibilityChange() {
    if (document.hidden) {
        clearInterval(refreshInterval);
    } else {
        startRealTimeUpdates();
    }
}

</script>
@endpush