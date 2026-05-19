@extends('admin.layouts.app')
@section('title', 'Household Requests')
@section('breadcrumb', 'Household Requests')

@section('content')
<div class="dash-header">
    <div>
        <h1>Household Requests</h1>
        <p class="sub">Manage household assistance requests</p>
    </div>
    <div>
        <a href="{{ route('barangay.household_requests.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> New Request
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="section-card">
    <h3>Your Household Requests</h3>
    @if($requests->isEmpty())
        <p>No household requests submitted yet.</p>
        <a href="{{ route('barangay.household_requests.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Submit First Request
        </a>
    @else
        <div class="table-responsive">
            <table class="dist-table">
                <thead>
                    <tr>
                        <th>Head of Household</th>
                        <th>Address</th>
                        <th>Family Members</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                    <tr>
                        <td>{{ $request->head_of_household }}</td>
                        <td>{{ $request->address }}</td>
                        <td>{{ $request->members->count() + 1 }} members</td>
                        <td>
                            <span class="status-badge {{ $request->status }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('barangay.household_requests.show', $request->id) }}" class="btn-sm-secondary">
                                View
                            </a>
                            @if($request->isPending())
                                <a href="{{ route('barangay.household_requests.edit', $request->id) }}" class="btn-sm-primary" style="margin-left: 5px;">
                                    Edit
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<style>
.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.pending {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.approved {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.rejected {
    background: #fee2e2;
    color: #991b1b;
}
</style>
@endsection
