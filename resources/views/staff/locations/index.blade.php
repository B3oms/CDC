@extends('staff.layouts.app')
@section('title', 'Location Management')

@section('content')
<div class="dash-header">
    <h1>Location Management</h1>
    <a href="{{ route('staff.staff.locations.create') }}" class="btn-primary">+ Add Location</a>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($municipalities->isEmpty() && $barangays->isEmpty())
<div class="section-card" style="text-align:center;padding:3rem;">
    <p style="color:#888;font-size:1rem;">No locations yet.</p>
    <a href="{{ route('staff.staff.locations.create') }}" class="btn-primary" style="margin-top:1rem;display:inline-block;">
        Add First Location
    </a>
</div>
@else

{{-- Municipalities --}}
@if($municipalities->count())
<div class="relief-section">
    <div class="relief-section-title">Municipalities</div>
    <div class="relief-grid">
        @foreach($municipalities as $municipality)
        <div class="section-card">
            <h3>{{ $municipality->name }}</h3>
            <p style="color:#666;font-size:0.9rem;">Province: {{ $municipality->province }}</p>
            <div style="margin-top:1rem;display:flex;gap:0.5rem;">
                <a href="{{ route('staff.locations.edit-municipality', $municipality->id) }}" class="btn-secondary">Edit</a>
                <form method="POST" action="{{ route('staff.locations.destroy-municipality', $municipality->id) }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger" onclick="return confirm('Delete this municipality?')">Delete</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Barangays --}}
@if($barangays->count())
<div class="relief-section">
    <div class="relief-section-title">Barangays</div>
    <div class="relief-grid">
        @foreach($barangays as $barangay)
        <div class="section-card">
            <h3>{{ $barangay->name }}</h3>
            <p style="color:#666;font-size:0.9rem;">Municipality: {{ $barangay->municipality->name }}</p>
            <div style="margin-top:1rem;display:flex;gap:0.5rem;">
                <a href="{{ route('staff.locations.edit-barangay', $barangay->id) }}" class="btn-secondary">Edit</a>
                <form method="POST" action="{{ route('staff.locations.destroy-barangay', $barangay->id) }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger" onclick="return confirm('Delete this barangay?')">Delete</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endif
@endsection