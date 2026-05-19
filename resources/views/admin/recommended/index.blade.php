@extends('admin.layouts.app')
@section('title', 'Recommended Beneficiaries')
@section('breadcrumb', 'Recommended Beneficiaries')

@section('content')
<div class="dash-header">
    <h1>Recommended Beneficiaries</h1>
</div>

{{-- Filter --}}
<div style="margin-bottom:1rem;">
    <form method="GET" action="{{ route('recommended.index') }}" style="display:flex;gap:1rem;align-items:center;">
        <select name="municipality_id" onchange="this.form.submit()"
            style="padding:6px 12px;border:1px solid #d3d1c7;border-radius:6px;font-size:13px;">
            <option value="">All Municipalities</option>
            @foreach($municipalities as $m)
            <option value="{{ $m->id }}" {{ request('municipality_id') == $m->id ? 'selected' : '' }}>
                {{ $m->name }}
            </option>
            @endforeach
        </select>
        <select name="barangay_id" onchange="this.form.submit()"
            style="padding:6px 12px;border:1px solid #d3d1c7;border-radius:6px;font-size:13px;">
            <option value="">All Barangays</option>
            @foreach($barangays as $b)
            <option value="{{ $b->id }}" {{ request('barangay_id') == $b->id ? 'selected' : '' }}>
                {{ $b->name }}
            </option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()"
            style="padding:6px 12px;border:1px solid #d3d1c7;border-radius:6px;font-size:13px;">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
        </select>
    </form>
</div>

@if($recommended->isEmpty())
    <div class="section-card" style="text-align:center;padding:3rem;">
        <p style="color:#888;">No recommended beneficiaries found.</p>
    </div>
@else
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
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recommended as $i => $r)
                <tr>
                    <td>{{ $recommended->firstItem() + $i }}</td>
                    <td>{{ $r->first_name }}{{ $r->middle_name ? ' ' . $r->middle_name : '' }} {{ $r->last_name }}</td>
                    <td>{{ $r->gender ?? 'N/A' }}</td>
                    <td>{{ $r->age ?? 'N/A' }}</td>
                    <td>{{ $r->barangay->name ?? 'N/A' }}</td>
                    <td>{{ $r->contact_number ?? 'N/A' }}</td>
                    <td>{{ $r->address ?? 'N/A' }}</td>
                    <td>
                        <span class="status-badge status-{{ $r->status }}">
                            {{ ucfirst($r->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('recommended.show', $r->id) }}" class="btn-sm-primary">View</a>
                        @if($r->status == 'pending')
                            <form action="{{ route('recommended.approve', $r->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-sm-success">Approve</button>
                            </form>
                            <form action="{{ route('recommended.reject', $r->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-sm-warning">Reject</button>
                            </form>
                            <form action="{{ route('recommended.convert', $r->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-sm-info">Convert</button>
                            </form>
                        @endif
                        <form action="{{ route('recommended.destroy', $r->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-sm-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        {{ $recommended->links() }}
    </div>
@endif

<style>
.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
}
.status-pending {
    background-color: #fff3cd;
    color: #856404;
}
.status-approved {
    background-color: #d4edda;
    color: #155724;
}
.status-rejected {
    background-color: #f8d7da;
    color: #721c24;
}
.status-converted {
    background-color: #cce5ff;
    color: #004085;
}
</style>
@endsection
