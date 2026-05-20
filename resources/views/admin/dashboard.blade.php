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
        <div class="stat-num" style="color:#dc3545;" id="lowStockItems">{{ $lowStockItems ?? 0 }}</div>
        <div class="stat-label"><i class="fas fa-exclamation-triangle"></i> Low Stock Alerts</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#ffc107;" id="expiringItems">{{ $expiringItems ?? 0 }}</div>
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
            <button onclick="toggleChartPdfDropdown(event, 'monthly')" class="pdf-export-btn">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
            <div id="pdfOptions-monthly" class="pdf-options" style="display:none;position:absolute;top:100%;right:0;background:white;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);padding:12px;min-width:200px;z-index:1001;">
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Paper Size</label>
                    <select id="paperSize-monthly" style="width:100%;padding:6px 8px;border:1px solid #d1d5db;border-radius:4px;font-size:13px;color:#374151;">
                        <option value="A4">A4</option>
                        <option value="Letter">Letter</option>
                        <option value="Legal">Legal</option>
                    </select>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Orientation</label>
                    <select id="orientation-monthly" style="width:100%;padding:6px 8px;border:1px solid #d1d5db;border-radius:4px;font-size:13px;color:#374151;">
                        <option value="portrait" selected>Portrait</option>
                        <option value="landscape">Landscape</option>
                    </select>
                </div>
                <button onclick="exportChartToPDF('chart-monthly', 'monthly-trend', 'monthly')" style="width:100%;padding:8px;background:#10b981;color:white;border:none;border-radius:4px;font-size:13px;font-weight:500;cursor:pointer;transition:background 0.2s;"
                   onmouseover="this.style.background='#059669'"
                   onmouseout="this.style.background='#10b981'">
                    Export PDF
                </button>
            </div>
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
            <button onclick="toggleChartPdfDropdown(event, 'yearly')" class="pdf-export-btn">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
            <div id="pdfOptions-yearly" class="pdf-options" style="display:none;position:absolute;top:100%;right:0;background:white;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);padding:12px;min-width:200px;z-index:1001;">
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Paper Size</label>
                    <select id="paperSize-yearly" style="width:100%;padding:6px 8px;border:1px solid #d1d5db;border-radius:4px;font-size:13px;color:#374151;">
                        <option value="A4">A4</option>
                        <option value="Letter">Letter</option>
                        <option value="Legal">Legal</option>
                    </select>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Orientation</label>
                    <select id="orientation-yearly" style="width:100%;padding:6px 8px;border:1px solid #d1d5db;border-radius:4px;font-size:13px;color:#374151;">
                        <option value="portrait" selected>Portrait</option>
                        <option value="landscape">Landscape</option>
                    </select>
                </div>
                <button onclick="exportChartToPDF('chart-yearly-trend', 'yearly-trend', 'yearly')" style="width:100%;padding:8px;background:#10b981;color:white;border:none;border-radius:4px;font-size:13px;font-weight:500;cursor:pointer;transition:background 0.2s;"
                   onmouseover="this.style.background='#059669'"
                   onmouseout="this.style.background='#10b981'">
                    Export PDF
                </button>
            </div>
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
                    @foreach($completedEvents->take(5) as $event)
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
<style>
/* ─── Dashboard Header ───────────────────────────────── */
.dash-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.dash-header h1 {
    color: #2c2c2a;
    font-size: 1.75rem;
    font-weight: 600;
    margin: 0;
}

/* ─── Calamity Meter ─────────────────────────────────── */
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

.calamity-meter.none { background: #eaf3de; border-color: #639922; }
.calamity-meter.none .cal-label { color: #3b6d11; }
.calamity-meter.none .cal-name  { color: #27500a; }

/* ─── Charts Row ─────────────────────────────────────── */
.charts-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.chart-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    min-width: 0;
}

.chart-title {
    font-size: 12px;
    font-weight: 700;
    color: #2c2c2a;
    letter-spacing: 0.04em;
    margin-bottom: 0.75rem;
}

canvas { max-width: 100% !important; height: auto !important; }
.chart-card canvas { max-height: 200px; }

.chart-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 8px;
}

.pdf-export-btn {
    background: #dc3545;
    color: #fff;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    margin-top: 8px;
    transition: background 0.2s;
}

.pdf-export-btn:hover { background: #c82333; }
.pdf-export-btn i { margin-right: 4px; }

/* ─── Bottom Grid ────────────────────────────────────── */
.bottom-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

/* ─── Section Cards (scoped to dashboard) ────────────── */
.db-section-card {
    background: #fff !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 8px !important;
    padding: 1rem 1.25rem !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08) !important;
    min-width: 0;
    overflow: hidden;
}

.db-section-title {
    font-size: 13px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.04em !important;
    color: #2c2c2a !important;
    margin: 0 0 1rem 0 !important;
    background: transparent !important;
    padding: 0 !important;
}

/* ─── Table Scroll Wrapper ───────────────────────────── */
.db-table-scroll {
    overflow-x: auto;
    overflow-y: scroll;
    -webkit-overflow-scrolling: touch;
    width: 100%;
    max-height: 200px;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
}

.db-table-scroll::-webkit-scrollbar {
    width: 8px;
}

.db-table-scroll::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.db-table-scroll::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 4px;
}

.db-table-scroll::-webkit-scrollbar-thumb:hover {
    background: #999;
}

/* ─── Dashboard Tables (fully scoped, no conflicts) ──── */
.db-table {
    width: 100%;
    min-width: 460px;
    border-collapse: collapse;
    font-size: 13px;
    background: #fff !important;
    color: #333 !important;
}

.db-table thead tr {
    background: #f8f9fa !important;
}

.db-table th {
    padding: 10px 12px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: #495057 !important;
    border-bottom: 2px solid #dee2e6;
    white-space: nowrap;
    background: #f8f9fa !important;
}

.db-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #f0f0f0;
    color: #333 !important;
    background: #fff !important;
    vertical-align: middle;
}

.db-table tbody tr:last-child td {
    border-bottom: none;
}

.db-table tbody tr td {  
    color: #185fa5 !important;
    text-decoration: none;
}

.db-link:hover {
    text-decoration: underline;
}

/* ─── Status Badges ──────────────────────────────────── */
.relief-status-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    white-space: nowrap;
}

.relief-status-badge.ongoing  { background: #fef3c7; color: #d97706; }
.relief-status-badge.upcoming { background: #dbeafe; color: #1e40af; }
.relief-status-badge.done     { background: #d1fae5; color: #059669; }

/* ─── Stat animation ─────────────────────────────────── */
.stat-num { transition: all 0.3s ease; }
.stat-num.updating { color: #10b981; transform: scale(1.1); }

/* ─── Responsive ─────────────────────────────────────── */
@media (max-width: 1024px) {
    .charts-row  { grid-template-columns: 1fr; }
    .bottom-grid { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .dash-header h1 { font-size: 1.25rem; }

    .stats-row {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }

    .stat-card  { padding: 0.75rem; }
    .stat-num   { font-size: 1.3rem; }
    .stat-label { font-size: 0.75rem; }

    .charts-row  { gap: 1rem; }
    .bottom-grid { gap: 1rem; }

    .db-section-card { padding: 0.875rem !important; }

    .db-table {
        min-width: 400px;
        font-size: 12px;
    }

    .db-table th,
    .db-table td {
        padding: 8px 10px;
    }
}

@media (max-width: 480px) {
    .dash-header { margin-bottom: 1.25rem; }
    .dash-header h1 { font-size: 1.1rem; }

    .stats-row {
        grid-template-columns: 1fr 1fr;
        gap: 0.4rem;
    }

    .stat-card  { padding: 0.6rem; }
    .stat-num   { font-size: 1.1rem; }
    .stat-label { font-size: 0.65rem; }

    .chart-card { padding: 0.75rem; }

    .db-section-card { padding: 0.75rem !important; }

    .db-table {
        min-width: 360px;
        font-size: 11px;
    }

    .db-table th,
    .db-table td {
        padding: 7px 8px;
    }
}
</style>
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
    document.getElementById(`pdfOptions-${chartType}`).style.display = 'none';
}

let dropdownOpenTime = 0;

function toggleChartPdfDropdown(event, chartType) {
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }
    const dropdown = document.getElementById(`pdfOptions-${chartType}`);
    if (dropdown.style.display === 'none') {
        dropdown.style.display = 'block';
        dropdownOpenTime = Date.now();
    } else {
        dropdown.style.display = 'none';
    }
}

// Prevent dropdown from closing when clicking inside
['monthly', 'yearly'].forEach(chartType => {
    const dropdown = document.getElementById(`pdfOptions-${chartType}`);
    if (dropdown) {
        dropdown.addEventListener('click', function(event) {
            event.stopPropagation();
            event.preventDefault();
        });
    }
});

// Close dropdowns when clicking outside (with delay to prevent immediate closing)
document.addEventListener('click', function(event) {
    ['monthly', 'yearly'].forEach(chartType => {
        const dropdown = document.getElementById(`pdfOptions-${chartType}`);
        const button = event.target.closest('.pdf-export-btn');
        const insideDropdown = event.target.closest(`#pdfOptions-${chartType}`);
        
        // Don't close if just opened (within 200ms)
        if (Date.now() - dropdownOpenTime < 200) {
            return;
        }
        
        if (!button && !insideDropdown && dropdown && dropdown.style.display === 'block') {
            dropdown.style.display = 'none';
        }
    });
});

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