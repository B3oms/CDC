@extends('admin.layouts.app')
@section('title', 'Calamity Report')

@section('content')
<div class="dash-header">
    <div>
        <h1>Final Report — {{ $calamity->name }}</h1>
        <p class="sub">{{ $calamity->type }} · {{ $calamity->date_occurred }} · 
            <span class="badge-intensity {{ strtolower($calamity->intensity) }}">{{ $calamity->intensity }}</span>
        </p>
    </div>
    <x-back-button href="{{ route('admin.dashboard') }}" label="Back to Dashboard" />
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
@endsection