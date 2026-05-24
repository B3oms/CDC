@extends('staff.layouts.app')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')

<div class="dash-header">
    <h1>Welcome back, {{ auth()->user()->first_name }}!</h1>

    <a href="{{ route('staff.calamities.index') }}"
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
        <div class="stat-num">{{ $barangayCount }}</div>
        <div class="stat-label"><i class="fas fa-map"></i> Barangays</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $municipalityCount }}</div>
        <div class="stat-label"><i class="fas fa-city"></i> Municipalities</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#185fa5;">{{ $upcomingEvents->count() }}</div>
        <div class="stat-label"><i class="fas fa-calendar-alt"></i> Upcoming Events</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#3b6d11;">{{ $completedEvents->count() }}</div>
        <div class="stat-label"><i class="fas fa-check-circle"></i> Completed Events</div>
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

    @if($upcomingEvents->count())
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

    @if($completedEvents->count())
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
});

function exportChartPdf(chartType) {
    const paperSize = document.getElementById(`paperSize-${chartType}`).value;
    const orientation = document.getElementById(`orientation-${chartType}`).value;
    const url = `{{ route('staff.dashboard.chart.pdf', ['type' => '__type__']) }}`.replace('__type__', chartType);

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

    closePdfDropdown(`pdfOptions-${chartType}`);
}
</script>
@endpush
