@extends('admin.layouts.app')
@section('title', 'Relief Monitor')

@section('content')
<div class="dash-header">
    <h1>Relief Monitor</h1>
    <a href="{{ route('admin.relief.create') }}" class="btn-primary">+ Create Event</a>
</div>

{{-- Stats Row --}}
<div class="stats-row" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-num">{{ $ongoingCount ?? 0 }}</div>
        <div class="stat-label">Ongoing Events</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $upcomingCount ?? 0 }}</div>
        <div class="stat-label">Upcoming Events</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $completedCount ?? 0 }}</div>
        <div class="stat-label">Completed Events</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $totalBeneficiaries ?? 0 }}</div>
        <div class="stat-label">Total Beneficiaries</div>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($events->isEmpty())
<div class="section-card" style="text-align:center;padding:3rem;">
    <p style="color:#888;font-size:1rem;">No relief events yet.</p>
    <a href="{{ route('admin.relief.create') }}" class="btn-primary" style="margin-top:1rem;display:inline-block;">
        Create First Event
    </a>
</div>
@else

{{-- Ongoing --}}
@if($events->where('status', 'Ongoing')->count())
<div class="relief-section">
    <div class="relief-section-title">Ongoing</div>
    <div class="relief-grid">
        @foreach($events->where('status', 'Ongoing') as $event)
            @include('admin.relief.partials.card', ['event' => $event])
        @endforeach
    </div>
</div>
@endif

{{-- Upcoming --}}
@if($events->where('status', 'Upcoming')->count())
<div class="relief-section">
    <div class="relief-section-title">Upcoming</div>
    <div class="relief-grid">
        @foreach($events->where('status', 'Upcoming') as $event)
            @include('admin.relief.partials.card', ['event' => $event])
        @endforeach
    </div>
</div>
@endif

{{-- Done --}}
@if($events->where('status', 'Done')->count())
<div class="relief-section">
    <div class="relief-section-title">Completed</div>
    <div class="relief-grid">
        @foreach($events->where('status', 'Done') as $event)
            @include('admin.relief.partials.card', ['event' => $event])
        @endforeach
    </div>
</div>
@endif

@endif
@endsection