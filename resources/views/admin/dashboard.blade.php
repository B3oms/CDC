@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('breadcrumb', '<i class="fas fa-tachometer-alt"></i> Dashboard')

@section('content')

<div class="page-header">
    <div class="page-title">
        <h1>Welcome back, {{ auth()->user()->first_name }}!</h1>
        <p class="page-description">Here's what's happening with your relief management system today</p>
    </div>
    <div class="page-actions">
        @if($activeCalamity)
        <a href="{{ route('admin.calamity.show', $activeCalamity->id) }}" class="btn btn-warning">
            <i class="fas fa-exclamation-triangle"></i> View Active Calamity
        </a>
        @else
        <a href="{{ route('admin.calamity.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Create Calamity
        </a>
        @endif
    </div>
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
        <div class="stat-num">{{ $barangayCount ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-map"></i> Total Barangays
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $municipalityCount ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-city"></i> Municipalities
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $totalDistributions ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-boxes"></i> Total Distributions
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $beneficiariesThisYear ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-users"></i> Beneficiaries This Year
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $verifiedBeneficiaries ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-check-circle"></i> Verified Beneficiaries
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $totalInventoryItems ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-box"></i> Total Inventory Items
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#dc3545;">{{ $lowStockItems ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-exclamation-triangle"></i> Low Stock Alerts
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#ffc107;">{{ $expiringItems ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-clock"></i> Expiring Items (30 days)
            <i class="fas fa-check-circle"></i> Completed Events
        </div>
    </div>
</div>

{{-- Location Management Stats --}}
<div class="stats-row" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-num" style="color:#ef9f27;">
            {{ \App\Models\Municipality::pending()->count() }}
        </div>
        <div class="stat-label">
            <i class="fas fa-clock"></i> Pending Locations
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#3b6d11;">
            {{ \App\Models\Municipality::approved()->count() }}
        </div>
        <div class="stat-label">
            <i class="fas fa-check"></i> Approved Locations
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#e24b4a;">
            {{ \App\Models\Municipality::rejected()->count() }}
        </div>
        <div class="stat-label">
            <i class="fas fa-times"></i> Rejected Locations
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#888780;">
            {{ \App\Models\User::whereHas('role', function($q) { $q->where('name', 'Staff'); })->count() }}
        </div>
        <div class="stat-label">
            <i class="fas fa-users"></i> Active Staff
        </div>
    </div>
</div>

<div class="dash-grid">

    {{-- LEFT: Charts --}}
    <div class="yearly-col">

        {{-- Monthly chart for current year --}}
        <div class="chart-card">
            <div class="chart-title">{{ now()->year }} — Monthly Relief Events</div>
            <canvas id="chart-monthly" height="100"></canvas>
        </div>

        {{-- Yearly charts --}}
        @forelse($yearlyData as $yearData)
        <div class="chart-card">
            <div class="chart-title">{{ $yearData->year }} — Relief Events</div>
            <canvas id="chart-{{ $yearData->year }}"
                data-labels="{{ json_encode(['Total', 'Completed']) }}"
                data-values="{{ json_encode([$yearData->total_distributions, $yearData->completed_distributions]) }}"
                height="80">
            </canvas>
            <a href="{{ route('admin.relief.index') }}">View →</a>
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

    {{-- Yearly Distribution Charts --}}
    <div class="chart-section" style="grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
        <div class="chart-card">
            <h3>Yearly Distribution Reports</h3>
            <div style="height: 300px;">
                <canvas id="yearlyChart"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h3>Monthly Distribution Trend</h3>
            <div style="height: 300px;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Calamity Types Handled --}}
    <div class="chart-section" style="margin-bottom: 2rem;">
        <div class="chart-card">
            <h3>Calamity Types Handled</h3>
            <div style="height: 250px;">
                <canvas id="calamityChart"></canvas>
            </div>
        </div>
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
<script>
const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

// Monthly chart for current year
const monthlyRaw   = @json($monthlyData);
const monthlyLabels = monthNames;
const monthlyValues = monthNames.map((_, i) => {
    const found = monthlyRaw.find(m => m.month === i + 1);
    return found ? found.total : 0;
});

new Chart(document.getElementById('chart-monthly'), {
    type: 'bar',
    data: {
        labels: monthlyLabels,
        datasets: [{
            data: monthlyValues,
            backgroundColor: 'rgba(26,61,31,0.7)',
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 9 } } },
            y: { beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});

// Yearly line charts
document.querySelectorAll('[id^="chart-"]:not(#chart-monthly)').forEach(canvas => {
    const labels = JSON.parse(canvas.dataset.labels).map(m => monthNames[m - 1]);
    const values = JSON.parse(canvas.dataset.values);
    new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                data: values,
                borderColor: '#1a3d1f',
                backgroundColor: 'rgba(26,61,31,0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 3,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 9 } } },
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });
});

// Yearly distribution line chart
const yearlyRaw = @json($yearlyData);
const yearlyLabels = yearlyRaw.map(y => y.year);
const yearlyDistributions = yearlyRaw.map(y => y.total_distributions);
const yearlyCompleted = yearlyRaw.map(y => y.completed_distributions);

new Chart(document.getElementById('yearlyChart'), {
    type: 'line',
    data: {
        labels: yearlyLabels,
        datasets: [
            {
                label: 'Total Distributions',
                data: yearlyDistributions,
                borderColor: 'rgba(26,61,31,1)',
                backgroundColor: 'rgba(26,61,31,0.1)',
                tension: 0.1
            },
            {
                label: 'Completed Distributions',
                data: yearlyCompleted,
                borderColor: 'rgba(75,192,192,1)',
                backgroundColor: 'rgba(75,192,192,0.1)',
                tension: 0.1
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: true } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 9 } } },
            y: { beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});

// Calamity types pie chart
const calamityRaw = @json($calamityTypes);
const calamityLabels = calamityRaw.map(c => c.first_letter.toUpperCase());
const calamityData = calamityRaw.map(c => c.count);

new Chart(document.getElementById('calamityChart'), {
    type: 'pie',
    data: {
        labels: calamityLabels,
        datasets: [{
            data: calamityData,
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40',
                '#FF6384',
                '#C9CBCF'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Top priority barangays horizontal bar chart
const barangayRaw = @json($topBarangays);
const barangayLabels = barangayRaw.map(b => b.name);
const barangayData = barangayRaw.map(b => b.distribution_count);

new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: barangayLabels,
        datasets: [{
            label: 'Distributions Received',
            data: barangayData,
            backgroundColor: 'rgba(54,162,235,0.8)',
            borderColor: 'rgba(54,162,235,1)',
            borderWidth: 1
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true }
        }
    }
});
</script>
@endpush