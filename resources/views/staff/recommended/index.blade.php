@extends('staff.layouts.app')
@section('title', 'Recommended Beneficiaries')

@section('content')
<div class="dash-header">
    <h1>Recommended Beneficiaries</h1>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

{{-- Filter --}}
<div style="margin-bottom:1rem;">
    <form method="GET" action="{{ route('staff.recommended.index') }}">
        <select name="barangay_id" onchange="this.form.submit()"
            style="padding:6px 12px;border:1px solid #d3d1c7;border-radius:6px;font-size:13px;">
            <option value="">All Barangays</option>
            @foreach($barangays as $b)
            <option value="{{ $b->id }}" {{ $barangayId == $b->id ? 'selected' : '' }}>
                {{ $b->name }}
            </option>
            @endforeach
        </select>
    </form>
</div>

<div class="section-card">
    <table class="dist-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Barangay</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Submitted by</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recommended as $i => $r)
            <tr>
                <td>{{ $recommended->firstItem() + $i }}</td>
                <td>{{ $r->first_name }}{{ $r->middle_name ? ' ' . $r->middle_name : '' }} {{ $r->last_name }}</td>
                <td>{{ $r->gender ?? 'N/A' }}</td>
                <td>{{ $r->age ?? 'N/A' }}</td>
                <td>{{ $r->barangay->name }}</td>
                <td>{{ $r->contact_number ?? 'N/A' }}</td>
                <td>{{ $r->address ?? 'N/A' }}</td>
                <td>{{ $r->submittedBy->first_name ?? 'N/A' }}</td>
                <td>
                    @if($r->status === 'Pending')
                        <span class="relief-status-badge upcoming">Pending</span>
                    @elseif($r->status === 'Converted')
                        <span class="relief-status-badge ongoing">Converted</span>
                    @else
                        <span class="relief-status-badge done">Rejected</span>
                    @endif
                </td>
                <td style="display:flex;gap:6px;">
                    @if($r->status === 'Pending')
                    <a href="{{ route('staff.recommended.convert', $r->id) }}"
                        class="btn-sm-secondary">Interview</a>
                    <form method="POST" action="{{ route('staff.recommended.reject', $r->id) }}"
                        style="display:inline;"
                        onsubmit="return confirm('Reject this recommendation?')">
                        @csrf
                        <button type="submit" class="btn-sm-danger">Reject</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;color:#888;padding:20px;">
                    No recommendations yet.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="margin-top:1rem;">
        {{ $recommended->withQueryString()->links() }}
    </div>
</div>
@endsection