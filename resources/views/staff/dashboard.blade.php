@extends('staff.layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- ===== HEADER ===== --}}
<div class="dash-header">
    <div class="dash-greeting">
        <h1>Hello, {{ auth()->user()->first_name }}!</h1>
        <p class="dash-date">{{ now()->format('l, F j, Y') }}</p>
    </div>
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

{{-- ===== STATS ROW ===== --}}
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-num">{{ $barangayCount }}</div>
        <div class="stat-label">Barangays</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $municipalityCount }}</div>
        <div class="stat-label">Municipalities</div>
    </div>
    <div class="stat-card">
        <div class="stat-num upcoming-num">{{ $upcomingEvents->count() }}</div>
        <div class="stat-label">Upcoming Events</div>
    </div>
    <div class="stat-card">
        <div class="stat-num completed-num">{{ $completedEvents->count() }}</div>
        <div class="stat-label">Completed Events</div>
    </div>
</div>

{{-- ===== CHARTS SECTION ===== --}}
<div class="charts-section">

    {{-- Charts side by side --}}
    <div class="charts-row">
        {{-- Monthly Chart --}}
        <div class="chart-card">
            <div class="chart-title">Monthly Trend</div>
            <div class="chart-wrap">
                <canvas id="chart-monthly"></canvas>
            </div>
            <div class="chart-actions">
                <button onclick="toggleChartPdfDropdown(event, 'monthly')" class="pdf-export-btn">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
                <div id="pdfOptions-monthly" class="pdf-options" style="display:none;position:absolute;top:100%;right:0;background:white;border:1px solid #000000;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);padding:12px;min-width:200px;z-index:1001;">
                    <div style="margin-bottom:12px;">
                        <label style="display:block;font-size:12px;font-weight:600;color:#000000;margin-bottom:6px;">Paper Size</label>
                        <select id="paperSize-monthly" style="width:100%;padding:6px 8px;border:1px solid #000000;border-radius:4px;font-size:13px;color:#000000;">
                            <option value="A4">A4</option>
                            <option value="Letter">Letter</option>
                            <option value="Legal">Legal</option>
                        </select>
                    </div>
                    <div style="margin-bottom:12px;">
                        <label style="display:block;font-size:12px;font-weight:600;color:#000000;margin-bottom:6px;">Orientation</label>
                        <select id="orientation-monthly" style="width:100%;padding:6px 8px;border:1px solid #000000;border-radius:4px;font-size:13px;color:#000000;">
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

        {{-- Yearly Trend --}}
        @forelse($yearlyData as $year => $months)
        <div class="chart-card">
            <div class="chart-title">Yearly Trend</div>
            <div class="chart-wrap">
                <canvas id="chart-yearly-trend"
                    data-labels="{{ json_encode($yearlyTrendLabels) }}"
                    data-values="{{ json_encode($yearlyTrendValues) }}">
                </canvas>
            </div>
            <div class="chart-actions">
                <button onclick="toggleChartPdfDropdown(event, 'yearly')" class="pdf-export-btn">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
                <div id="pdfOptions-yearly" class="pdf-options" style="display:none;position:absolute;top:100%;right:0;background:white;border:1px solid #000000;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);padding:12px;min-width:200px;z-index:1001;">
                    <div style="margin-bottom:12px;">
                        <label style="display:block;font-size:12px;font-weight:600;color:#000000;margin-bottom:6px;">Paper Size</label>
                        <select id="paperSize-yearly" style="width:100%;padding:6px 8px;border:1px solid #000000;border-radius:4px;font-size:13px;color:#000000;">
                            <option value="A4">A4</option>
                            <option value="Letter">Letter</option>
                            <option value="Legal">Legal</option>
                        </select>
                    </div>
                    <div style="margin-bottom:12px;">
                        <label style="display:block;font-size:12px;font-weight:600;color:#000000;margin-bottom:6px;">Orientation</label>
                        <select id="orientation-yearly" style="width:100%;padding:6px 8px;border:1px solid #000000;border-radius:4px;font-size:13px;color:#000000;">
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
        <div class="chart-card empty-chart">
            <div class="chart-title">No relief data yet</div>
            <p>Create relief events to see charts.</p>
        </div>
        @endforelse
    </div>

</div>{{-- /.charts-section --}}

{{-- ===== EVENTS SECTION ===== --}}
<div class="events-section">

    {{-- Upcoming & Ongoing Events --}}
    @if($upcomingEvents->count())
    <div class="section-card events-card">
        <h3><i class="fas fa-calendar-alt"></i> Upcoming & Ongoing Relief Events</h3>
        <div class="scrollable-table">
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
                            <a href="{{ route('admin.relief.show', $event->id) }}" class="table-link">
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

    {{-- Completed Events --}}
    @if($completedEvents->count())
    <div class="section-card">
        <h3><i class="fas fa-check-circle"></i> Completed Relief Events</h3>
        <div class="scrollable-table">
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
                            <a href="{{ route('admin.relief.show', $event->id) }}" class="table-link">
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

</div>{{-- /.events-section --}}

@endsection

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
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Yearly Trend Chart
    const yearlyCanvas = document.getElementById('chart-yearly-trend');
    if (yearlyCanvas && yearlyCanvas.dataset.labels) {
        const yearLabels = JSON.parse(yearlyCanvas.dataset.labels);
        const yearValues = JSON.parse(yearlyCanvas.dataset.values);
        new Chart(yearlyCanvas, {
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
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
});

function exportChartPdf(chartType) {
    const paperSize = document.getElementById(`paperSize-${chartType}`).value;
    const orientation = document.getElementById(`orientation-${chartType}`).value;
    const url = `{{ route('staff.dashboard.chart.pdf', ['type' => '__type__']) }}`.replace('__type__', chartType);
    
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
</script>
@endpush

@push('styles')
<style>
/* ============================================
   CSS VARIABLES
   ============================================ */
:root {
    --primary:       #1a3d1f;
    --primary-light: #2d6a35;
    --bg:            #ffffff;
    --white:         #ffffff;
    --border:        #000000;
    --text:          #2c2c2a;
    --muted:         #2c2c2a;
    --blue:          #185fa5;
    --green:         #3b6d11;
    --amber:         #ef9f27;
    --red:           #e24b4a;

    --radius-sm: 6px;
    --radius-md: 8px;
    --radius-lg: 12px;
    --shadow:    0 2px 6px rgba(0,0,0,.08);
    --shadow-md: 0 4px 14px rgba(0,0,0,.12);
}

/* ============================================
   HEADER
   ============================================ */
.dash-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.dash-greeting h1 {
    font-size: clamp(1.25rem, 4vw, 1.875rem);
    font-weight: 700;
    color: var(--primary);
    margin: 0 0 0.25rem;
    line-height: 1.2;
}

.dash-date {
    font-size: 0.8rem;
    color: var(--muted);
    margin: 0;
}

/* ============================================
   CALAMITY METER
   ============================================ */
.calamity-meter {
    background: transparent;
    border: 1px solid var(--amber);
    border-radius: var(--radius-md);
    padding: 8px 14px;
    text-align: right;
    min-width: 130px;
    flex-shrink: 0;
}

.cal-label {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: #b8860b;
    margin-bottom: 2px;
    font-weight: 500;
}

.cal-name {
    font-size: 11px;
    font-weight: 600;
    color: #633806;
    line-height: 1.3;
}

.cal-badge {
    display: inline-block;
    background: var(--red);
    color: #fff;
    font-size: 8px;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 500;
    margin-top: 3px;
}

.calamity-meter.none { background: transparent; border-color: #639922; }
.calamity-meter.none .cal-label { color: #3b6d11; }
.calamity-meter.none .cal-name  { color: #27500a; }

/* ============================================
   STATS ROW
   ============================================ */
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.25rem 1.5rem;
    box-shadow: var(--shadow);
    transition: box-shadow .2s;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.stat-card:hover { box-shadow: var(--shadow-md); }

.stat-num {
    font-size: clamp(1.5rem, 4vw, 2rem);
    font-weight: 700;
    color: var(--primary);
    line-height: 1;
    margin-bottom: 0.5rem;
}

.upcoming-num  { color: var(--blue); }
.completed-num { color: var(--green); }

.stat-label {
    font-size: 0.72rem;
    color: var(--muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: .4px;
    text-align: center;
    margin: 0 auto;
}

/* ============================================
   CHARTS SECTION
   ============================================ */
.charts-section {
    margin-bottom: 1.5rem;
}

/* ============================================
   EVENTS SECTION
   ============================================ */
.events-section {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

/* ============================================
   CHARTS ROW — side by side
   ============================================ */
.charts-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
}

/* ============================================
   CHART CARDS
   ============================================ */
.chart-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.25rem;
    box-shadow: var(--shadow);
}

.chart-title {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--text);
    text-transform: uppercase;
    letter-spacing: .6px;
    margin-bottom: 0.875rem;
    text-align: center;
}

/* Constrain canvas height so charts don't blow up */
.chart-wrap {
    position: relative;
    width: 100%;
}

.chart-wrap canvas {
    display: block;
    width: 100% !important;
    max-height: 180px;
}

.empty-chart p {
    font-size: 0.8rem;
    color: var(--muted);
    margin: 0.5rem 0 0;
    text-align: center;
}

/* ============================================
   PDF EXPORT BUTTON
   ============================================ */
.pdf-export-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: #dc3545;
    color: #fff;
    border: none;
    padding: 5px 12px;
    border-radius: var(--radius-sm);
    font-size: 0.75rem;
    cursor: pointer;
    margin-top: 0.75rem;
    transition: opacity .2s;
}

.pdf-export-btn:hover { opacity: .85; }

/* ============================================
   SECTION CARDS
   ============================================ */
.section-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.25rem;
    box-shadow: var(--shadow);
}

.section-card h3 {
    margin: 0 0 1rem;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-card h3 i {
    color: var(--primary);
    font-size: 0.85rem;
}

.events-card {
    flex: 1;
}

/* ============================================
   SCROLLABLE TABLE
   ============================================ */
.scrollable-table {
    overflow-x: auto;
    overflow-y: auto;
    max-height: 280px;
    border-radius: var(--radius-sm);
    border: 1px solid #000000;
    -webkit-overflow-scrolling: touch;
}

.dist-table {
    width: 100%;
    min-width: 380px;   /* prevents squish on small screens */
    border-collapse: collapse;
    font-size: 0.82rem;
}

.dist-table thead {
    position: sticky;
    top: 0;
    z-index: 1;
}

.dist-table th {
    background: transparent;
    color: var(--text);
    font-weight: 600;
    text-align: left;
    padding: 0.6rem 0.75rem;
    border-bottom: 2px solid var(--border);
    white-space: nowrap;
}

.dist-table td {
    padding: 0.6rem 0.75rem;
    border-bottom: 1px solid transparent;
    vertical-align: top;
    color: var(--text);
}

.dist-table tr:last-child td { border-bottom: none; }

.table-link {
    color: var(--blue);
    text-decoration: none;
    font-weight: 500;
}

.table-link:hover { text-decoration: underline; }

/* ============================================
   STATUS BADGES
   ============================================ */
.relief-status-badge {
    display: inline-block;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .3px;
    white-space: nowrap;
}

.relief-status-badge.upcoming  { background: transparent; color: var(--amber); border: 1px solid var(--amber); }
.relief-status-badge.ongoing   { background: transparent; color: var(--blue); border: 1px solid var(--blue); }
.relief-status-badge.completed { background: transparent; color: var(--green); border: 1px solid var(--green); }

/* ============================================
   RESPONSIVE — Tablet (≤ 1024px)
   ============================================ */
@media (max-width: 1024px) {
    .stats-row {
        grid-template-columns: repeat(2, 1fr);
    }

    .charts-row {
        grid-template-columns: 1fr;
    }
}

/* ============================================
   RESPONSIVE — Mobile (≤ 640px)
   ============================================ */
@media (max-width: 640px) {
    .dash-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .calamity-meter {
        align-self: stretch;
        text-align: left;
        min-width: unset;
    }

    .stats-row {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }

    .stat-card {
        padding: 1rem;
    }

    .charts-row {
        grid-template-columns: 1fr;
    }

    .chart-card,
    .section-card {
        padding: 1rem;
    }

    .chart-wrap canvas {
        max-height: 150px;
    }

    .scrollable-table {
        max-height: 220px;
    }
}

/* ============================================
   RESPONSIVE — Small phones (≤ 400px)
   ============================================ */
@media (max-width: 400px) {
    .stats-row {
        grid-template-columns: 1fr 1fr;
        gap: 0.625rem;
    }

    .stat-card {
        padding: 0.875rem 0.75rem;
    }

    .stat-num {
        font-size: 1.375rem;
    }

    .stat-label {
        font-size: 0.65rem;
    }

    .chart-card,
    .section-card {
        padding: 0.875rem;
    }

    .chart-wrap canvas {
        max-height: 130px;
    }
}
</style>
@endpush