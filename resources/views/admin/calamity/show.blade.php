@extends('staff.layouts.app')
@section('title', 'Calamity Portal')

@section('content')
<div class="dash-header">
    <div>
        <h1>{{ $calamity->name }}</h1>
        <p class="sub">{{ $calamity->type }} · {{ $calamity->date_occurred }}</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <a href="{{ route('admin.calamity.index') }}" class="btn-back">← Back</a>
        @if($calamity->status === 'Open')
        <span class="status-open">● PORTAL OPEN</span>
        <form method="POST" action="{{ route('admin.calamity.close', $calamity->id) }}">
            @csrf
            <button type="submit" class="btn-danger" onclick="return confirm('Close this portal and generate report?')">
                Close Portal
            </button>
        </form>
        @else
        <span class="status-closed">● PORTAL CLOSED</span>
        <a href="{{ route('admin.calamity.report', $calamity->id) }}" class="btn-primary">View Report</a>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="dash-grid">
    <div class="yearly-col">
        <div class="section-card">
            <h3>Partner Barangays</h3>
            @foreach($calamity->barangays as $barangay)
            <div class="partner-item">
                <span class="dot"></span> {{ $barangay->name }}
            </div>
            @endforeach
        </div>
    </div>

    <div class="right-col">
        <div class="section-card">
            <h3>Live Rankings — Top 10 Barangays</h3>
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
                        <td><strong>#{{ $i + 1 }}</strong></td>
                        <td>{{ $r->barangay->name }}</td>
                        <td>{{ $r->total_households }}</td>
                        <td>{{ $r->total_evacuees }}</td>
                        <td>{{ $r->max_severity }}/5</td>
                        <td><strong>{{ number_format($r->score, 2) }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:#888;padding:16px;">
                            No reports submitted yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection