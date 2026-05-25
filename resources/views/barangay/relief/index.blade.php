@extends('admin.layouts.app')
@section('title', 'Relief Events')
@section('breadcrumb', 'Relief Events')

@push('styles')
<style>
.stats-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.stat-card {
    flex: 1;
    min-width: 140px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 1.2rem 1.5rem;
    text-align: center;
}

.stat-card .stat-number {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.3rem;
}

.stat-card .stat-label {
    font-size: 0.8rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-card.ongoing  .stat-number { color: #f59e0b; }
.stat-card.upcoming .stat-number { color: #3b82f6; }
.stat-card.done     .stat-number { color: #10b981; }

.relief-table-wrapper {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
}

.relief-table {
    width: 100%;
    border-collapse: collapse;
}

.relief-table th {
    background: #f9fafb;
    padding: 0.85rem 1rem;
    text-align: left;
    font-size: 0.78rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid #e5e7eb;
}

.relief-table td {
    padding: 1rem;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.9rem;
    color: #374151;
    vertical-align: middle;
}

.relief-table tr:last-child td {
    border-bottom: none;
}

.relief-table tr:hover td {
    background: #f9fafb;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0.75rem;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 600;
}

.status-badge.ongoing  { background: #fef3c7; color: #92400e; }
.status-badge.upcoming { background: #dbeafe; color: #1e40af; }
.status-badge.done     { background: #d1fae5; color: #065f46; }

.event-name {
    font-weight: 600;
    color: #1a3d1f;
}

.event-meta {
    font-size: 0.8rem;
    color: #9ca3af;
    margin-top: 0.2rem;
}

.beneficiary-count {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: #f0fdf4;
    color: #166534;
    border-radius: 6px;
    padding: 0.25rem 0.6rem;
    font-size: 0.82rem;
    font-weight: 600;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #9ca3af;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    display: block;
    color: #d1d5db;
}
</style>
@endpush

@section('content')
<div class="dash-header">
    <h1>Relief Events</h1>
    <p style="color:#6b7280;margin-top:0.25rem;font-size:0.9rem;">
        Relief events scheduled for your barangay
    </p>
</div>

{{-- Stats --}}
<div class="stats-row">
    <div class="stat-card ongoing">
        <div class="stat-number">{{ $ongoingCount }}</div>
        <div class="stat-label">Ongoing</div>
    </div>
    <div class="stat-card upcoming">
        <div class="stat-number">{{ $upcomingCount }}</div>
        <div class="stat-label">Upcoming</div>
    </div>
    <div class="stat-card done">
        <div class="stat-number">{{ $doneCount }}</div>
        <div class="stat-label">Completed</div>
    </div>
</div>

{{-- Table --}}
<div class="relief-table-wrapper">
    @if($events->isEmpty())
        <div class="empty-state">
            <i class="fas fa-hands-helping"></i>
            <p>No relief events have been scheduled for your barangay yet.</p>
        </div>
    @else
        <table class="relief-table">
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Date</th>
                    <th>Venue</th>
                    <th>Calamity</th>
                    <th>Beneficiaries</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                <tr>
                    <td>
                        <div class="event-name">{{ $event->name }}</div>
                        <div class="event-meta">Created by {{ $event->creator->first_name ?? '—' }} {{ $event->creator->last_name ?? '' }}</div>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</td>
                    <td>{{ $event->venue }}</td>
                    <td>{{ $event->calamity_type ?? '—' }}</td>
                    <td>
                        <span class="beneficiary-count">
                            <i class="fas fa-users"></i>
                            {{ $event->barangay_beneficiary_count }}
                        </span>
                    </td>
                    <td>
                        @php $statusClass = strtolower($event->status); @endphp
                        <span class="status-badge {{ $statusClass }}">
                            @if($statusClass === 'ongoing') <i class="fas fa-circle" style="font-size:0.5rem;"></i>
                            @elseif($statusClass === 'upcoming') <i class="fas fa-clock"></i>
                            @else <i class="fas fa-check"></i>
                            @endif
                            {{ $event->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
