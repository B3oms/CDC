@extends('staff.layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="dash-header">
    <h1>Hello, {{ auth()->user()->first_name }}!</h1>

    @if($activeCalamity)
    <a href="{{ route('admin.calamity.show', $activeCalamity->id) }}"
        class="calamity-meter {{ strtolower($activeCalamity->intensity) }}"
        style="text-decoration:none;">
        <div class="cal-label">Calamity Meter ↗</div>
        <div class="cal-name">{{ $activeCalamity->name }}</div>
        <span class="cal-badge">{{ $activeCalamity->intensity }}</span>
    </a>
    @else
    <a href="{{ route('admin.calamity.create') }}"
        class="calamity-meter none" style="text-decoration:none;">
        <div class="cal-label">Calamity Meter</div>
        <div class="cal-name">+ Add Event</div>
    </a>
    @endif
</div>

{{-- Stats Row --}}
<div class="stats-row" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-num">{{ $barangayCount }}</div>
        <div class="stat-label">Barangays</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $municipalityCount }}</div>
        <div class="stat-label">Municipalities</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#185fa5;">
            {{ $upcomingEvents->count() }}
        </div>
        <div class="stat-label">Upcoming Events</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#3b6d11;">
            {{ $completedEvents->count() }}
        </div>
        <div class="stat-label">Completed Events</div>
    </div>
</div>

<div class="dash-grid">
    {{-- LEFT: Charts --}}
    <div class="yearly-col">
        <div class="chart-card">
            <div class="chart-title">{{ now()->year }} — Monthly Relief Events</div>
            <canvas id="chart-monthly" height="100"></canvas>
        </div>

        @forelse($yearlyData as $year => $months)
        <div class="chart-card">
            <div class="chart-title">{{ $year }} — Relief Events</div>
            <canvas id="chart-{{ $year }}"
                data-labels="{{ json_encode($months->pluck('month')) }}"
                data-values="{{ json_encode($months->pluck('total')) }}"
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
        </div>

        {{-- Upcoming & Ongoing --}}
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

        {{-- Completed --}}
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

        {{-- Partners --}}
        <div class="section-card">
            <h3>Partners</h3>
            <div class="partner-row">
                <div class="p-stat">
                    <div class="p-num">{{ $barangayCount }}</div>
                    <div class="p-label">Barangays</div>
                </div>
                <div class="p-stat">
                    <div class="p-num">{{ $municipalityCount }}</div>
                    <div class="p-label">Municipalities</div>
                </div>
                <div class="p-stat">
                    <div class="p-num">{{ $regionCount }}</div>
                    <div class="p-label">Regions</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

const monthlyRaw    = @json($monthlyData);
const monthlyValues = monthNames.map((_, i) => {
    const found = monthlyRaw.find(m => m.month === i + 1);
    return found ? found.total : 0;
});

new Chart(document.getElementById('chart-monthly'), {
    type: 'bar',
    data: {
        labels: monthNames,
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
</script>
@endpush