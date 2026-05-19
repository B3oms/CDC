@extends('admin.layouts.app')
@section('title', 'Household Requests')
@section('breadcrumb', 'Household Requests')

@section('content')
<div class="dash-header">
    <div>
        <h1>Household Requests</h1>
        <p class="sub">Review and approve household assistance requests</p>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-num">{{ $statistics['total'] }}</div>
        <div class="stat-label">Total Requests</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#fbbf24;">{{ $statistics['pending'] }}</div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#1a3d1f;">{{ $statistics['approved'] }}</div>
        <div class="stat-label">Approved</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#dc2626;">{{ $statistics['rejected'] }}</div>
        <div class="stat-label">Rejected</div>
    </div>
</div>

<div class="section-card" style="margin-top: 20px;">
    <h3>All Household Requests</h3>
    @if($requests->isEmpty())
        <p>No household requests submitted yet.</p>
    @else
        <div class="table-responsive">
            <table class="dist-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Head of Household</th>
                        <th>Barangay</th>
                        <th>Family Members</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                    <tr>
                        <td>#{{ $request->id }}</td>
                        <td>{{ $request->head_of_household }}</td>
                        <td>{{ $request->barangay->name }}</td>
                        <td>{{ $request->members->count() + 1 }} members</td>
                        <td>
                            <span class="status-badge {{ $request->status }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.household_requests.show', $request->id) }}" class="btn-sm-secondary">
                                Review
                            </a>
                            <form method="POST" action="{{ route('admin.household_requests.destroy', $request->id) }}"
                                style="display:inline; margin-left: 5px;"
                                onsubmit="return confirm('Delete this household request?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-sm-danger">Delete</button>
                            </form>
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
