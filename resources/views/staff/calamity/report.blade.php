@extends('staff.layouts.app')
@section('title', 'Calamity Report')

@section('content')
<div class="dash-header">
    <div>
        <h1>Final Report — {{ $calamity->name }}</h1>
        <p class="sub">{{ $calamity->type }} · {{ $calamity->date_occurred }} · 
            <span class="badge-intensity {{ strtolower($calamity->intensity) }}">{{ $calamity->intensity }}</span>
        </p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn-back">← Back to Dashboard</a>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="section-card">
    <h3>Top 10 Most Affected Barangays</h3>
    <table class="dist-table">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Barangay</th>
                <th>Households</th>
                <th>Evacuees</th>
                <th>Severity</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rankings as $i => $r)
            <tr class="{{ $i < 3 ? 'top-rank' : '' }}">
                <td>
                    @if($i == 0) 🥇
                    @elseif($i == 1) 🥈
                    @elseif($i == 2) 🥉
                    @else <strong>#{{ $i + 1 }}</strong>
                    @endif
                </td>
                <td>{{ $r->barangay->name }}</td>
                <td>{{ number_format($r->total_households) }}</td>
                <td>{{ number_format($r->total_evacuees) }}</td>
                <td>{{ $r->max_severity }}/5</td>
                <td><strong>{{ number_format($r->score, 2) }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;color:#888;padding:16px;">
                    No reports were submitted.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
<style>
.section-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.section-card h3 {
    margin: 0 0 1rem 0;
    font-size: 1.25rem;
    color: #1f2937;
}

.sub {
    color: #6b7280;
    margin: 0.25rem 0 0 0;
    font-size: 0.9rem;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: 1px solid #d1d5db;
    background: white;
    color: #6b7280;
    border-radius: 6px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-back:hover {
    background: #f9fafb;
    border-color: #9ca3af;
    color: #374151;
}

.alert-success {
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    color: #166534;
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1rem;
}

.dist-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    overflow: hidden;
}

.dist-table th,
.dist-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.dist-table th {
    background: #f9fafb;
    font-weight: 600;
    color: #1f2937;
}

.dist-table tbody tr:hover {
    background: #f9fafb;
}

.top-rank {
    background: #fef3cd;
}

.badge-intensity {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-left: 8px;
}

.badge-intensity.low {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.badge-intensity.medium {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.badge-intensity.high {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.badge-intensity.critical {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.badge-intensity.unknown {
    background: #e2e3e5;
    color: #383d41;
    border: 1px solid #d6d8db;
}
</style>
@endpush
@endsection
