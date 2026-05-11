@extends('admin.layouts.app')
@section('title', 'Relief Event Details')

@section('content')
<div class="dash-header">
    <div>
        <h1>{{ $event->name }}</h1>
        <p class="sub">
            {{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }} ·
            {{ $event->venue }} ·
            <span class="relief-status-badge {{ strtolower($event->status) }}">{{ $event->status }}</span>
        </p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <a href="{{ route('admin.relief.index') }}" class="btn-back">← Back</a>
        @if($event->status === 'Upcoming')
        <form method="POST" action="{{ route('admin.relief.ongoing', $event->id) }}">
            @csrf
            <button type="submit" class="btn-primary">Mark as Ongoing</button>
        </form>
        @elseif($event->status === 'Ongoing')
        <form method="POST" action="{{ route('admin.relief.done', $event->id) }}">
            @csrf
            <button type="submit" class="btn-danger" onclick="return confirm('Mark this event as done?')">
                Mark as Done
            </button>
        </form>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="dash-grid">
    <div class="yearly-col">

        {{-- Event Info --}}
        <div class="section-card">
            <h3>Event Details</h3>
            <table class="dist-table">
                <tr><td class="meta-label">Name</td><td>{{ $event->name }}</td></tr>
                <tr><td class="meta-label">Date</td><td>{{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</td></tr>
                <tr><td class="meta-label">Venue</td><td>{{ $event->venue }}</td></tr>
                <tr><td class="meta-label">Status</td><td><span class="relief-status-badge {{ strtolower($event->status) }}">{{ $event->status }}</span></td></tr>
                @if($event->calamity)
                <tr><td class="meta-label">Calamity</td><td>{{ $event->calamity->name }}</td></tr>
                @endif
            </table>
        </div>

        {{-- Facilitators --}}
        <div class="section-card">
            <h3>Facilitators</h3>
            @forelse($event->facilitators as $f)
            <div class="partner-item">
                <span class="dot"></span>
                {{ $f->first_name }} {{ $f->last_name }}
                <span class="role-tag">{{ $f->role->name }}</span>
            </div>
            @empty
            <p style="font-size:12px;color:#888;">No facilitators assigned.</p>
            @endforelse
        </div>

        {{-- Barangays --}}
        <div class="section-card">
            <h3>Barangays</h3>
            @foreach($barangays as $eb)
            <div class="partner-item">
                <span class="dot"></span>
                {{ $eb->barangay->name }}
                <span class="hint" style="margin:0;">{{ $eb->municipality->name }}</span>
            </div>
            @endforeach
        </div>

    </div>

    <div class="right-col">
        <div class="section-card">
            <h3>Beneficiaries</h3>

            {{-- Barangay Filter --}}
            <div style="margin-bottom:1rem;">
                <form method="GET" action="{{ route('admin.relief.show', $event->id) }}">
                    <select name="barangay_id" onchange="this.form.submit()"
                        style="padding:6px 12px;border:1px solid #d3d1c7;border-radius:6px;font-size:13px;">
                        <option value="">All Barangays</option>
                        @foreach($barangays as $eb)
                        <option value="{{ $eb->barangay_id }}"
                            {{ $selectedBarangayId == $eb->barangay_id ? 'selected' : '' }}>
                            {{ $eb->barangay->name }}
                        </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <table class="dist-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Barangay</th>
                        <th>Family Size</th>
                        <th>Vulnerability</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($beneficiaries as $i => $rb)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $rb->beneficiary->first_name }} {{ $rb->beneficiary->last_name }}</td>
                        <td>{{ $rb->beneficiary->barangay->name ?? 'N/A' }}</td>
                        <td>{{ $rb->beneficiary->family_size }}</td>
                        <td>
                            <span class="badge-intensity {{ strtolower($rb->beneficiary->vulnerability_level) }}">
                                {{ $rb->beneficiary->vulnerability_level }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;color:#888;padding:16px;">
                            No verified beneficiaries found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @if($beneficiaries->count())
            <div style="margin-top:1rem;text-align:right;">
                <a href="{{ route('admin.relief.show', $event->id) }}?{{ http_build_query(['barangay_id' => $selectedBarangayId, 'pdf' => 1]) }}"
                    class="btn-primary">
                    Export PDF
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection