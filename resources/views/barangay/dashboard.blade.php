@extends('admin.layouts.app')
@section('title', 'Barangay Dashboard')

@section('content')
<div class="dash-header">
    <h1>Hello, {{ auth()->user()->first_name }}!</h1>
    <span class="logo-circle" style="border-color:#1a3d1f;"></span>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($activeCalamity)
<div class="alert-calamity">
    <strong>Active Calamity: {{ $activeCalamity->name }}</strong>
    <span class="badge-intensity {{ strtolower($activeCalamity->intensity) }}">{{ $activeCalamity->intensity }}</span>
</div>

<div class="dash-grid">
    <div class="yearly-col">

        {{-- Evacuation Center Form --}}
        <div class="section-card">
            <h3>Evacuation Center</h3>
            <form method="POST" action="{{ route('barangay.setCenter') }}">
                @csrf
                <input type="hidden" name="calamity_id" value="{{ $activeCalamity->id }}">
                <div class="form-group">
                    <label>Venue</label>
                    <input type="text" name="venue" value="{{ $evacuationCenter->venue ?? '' }}" placeholder="e.g. Barangay Hall" required>
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" value="{{ $evacuationCenter->location ?? '' }}" placeholder="e.g. Purok 3, Sumacab Este" required>
                </div>
                <button type="submit" class="btn-primary" style="width:100%;margin-top:8px;">
                    {{ $evacuationCenter ? 'Update Center' : 'Set Center' }}
                </button>
            </form>
        </div>

        {{-- Report Form --}}
        @if($evacuationCenter)
        <div class="section-card">
            <h3>Submit Report</h3>
            <form method="POST" action="{{ route('barangay.submitReport') }}">
                @csrf
                <input type="hidden" name="calamity_id" value="{{ $activeCalamity->id }}">
                <input type="hidden" name="evacuation_center_id" value="{{ $evacuationCenter->id }}">

                <div class="form-group">
                    <label>Number of Households</label>
                    <input type="number" name="household_count" min="0"
                        value="{{ $latestReport->household_count ?? 0 }}" required>
                </div>
                <div class="form-group">
                    <label>Number of Evacuees</label>
                    <input type="number" name="evacuee_count" min="0"
                        value="{{ $latestReport->evacuee_count ?? 0 }}" required>
                </div>
                <div class="form-group">
                    <label>Severity Level (1-5)</label>
                    <select name="severity_level" required>
                        @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ ($latestReport->severity_level ?? 1) == $i ? 'selected' : '' }}>
                            {{ $i }} — {{ ['', 'Minor', 'Moderate', 'Serious', 'Critical', 'Catastrophic'][$i] }}
                        </option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="btn-primary" style="width:100%;margin-top:8px;">
                    Update Report
                </button>
            </form>
        </div>
        @endif

    </div>

    <div class="right-col">
        <div class="section-card">
            <h3>Live Rankings — Top 10</h3>
            <table class="dist-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Barangay</th>
                        <th>Households</th>
                        <th>Evacuees</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rankings as $i => $r)
                    <tr class="{{ $r->barangay_id == auth()->user()->barangay_id ? 'my-barangay' : '' }}">
                        <td><strong>#{{ $i + 1 }}</strong></td>
                        <td>{{ $r->barangay->name }}</td>
                        <td>{{ $r->total_households }}</td>
                        <td>{{ $r->total_evacuees }}</td>
                        <td><strong>{{ number_format($r->score, 2) }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;color:#888;padding:16px;">
                            No reports yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@else
<div class="section-card" style="text-align:center;padding:3rem;">
    <p style="font-size:1.1rem;color:#888;">No active calamity portal at the moment.</p>
    <p style="font-size:0.9rem;color:#aaa;margin-top:8px;">You will be notified when the CDC opens a portal.</p>
</div>
@endif
@endsection