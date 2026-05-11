@extends('admin.layouts.app')
@section('title', 'Location Requests')
@section('breadcrumb', '<i class="fas fa-map-marker-alt"></i> Location Management / My Requests')

@section('content')

<div class="page-header">
    <div class="page-title">
        <h1>My Location Requests</h1>
        <p class="page-description">Submit and track your location requests</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('staff.location-requests.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> New Request
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3>Request History</h3>
        <div class="card-actions">
            <select id="statusFilter" class="form-select" onchange="filterRequests(this.value)">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table" id="requestsTable">
            <thead>
                <tr>
                    <th>Location Name</th>
                    <th>Type</th>
                    <th>Municipality</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr data-status="{{ $request->status }}">
                        <td>
                            <strong>{{ $request->name }}</strong>
                            @if($request->remarks)
                                <br><small class="text-muted">{{ Str::limit($request->remarks, 50) }}</small>
                            @endif
                        </td>
                        <td>{!! $request->type_badge !!}</td>
                        <td>{{ $request->municipality->name ?? 'N/A' }}</td>
                        <td>{!! $request->status_badge !!}</td>
                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('staff.location-requests.show', $request->id) }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if($request->canBeEdited())
                                    <a href="{{ route('staff.location-requests.edit', $request->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                @endif
                                @if($request->canBeEdited())
                                    <form method="POST" action="{{ route('staff.location-requests.destroy', $request->id) }}" 
                                        style="display:inline;" 
                                        onsubmit="return confirm('Delete this location request?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-map-marked-alt" style="font-size: 2rem; color: #888780; margin-bottom: 0.5rem;"></i>
                                <p>No location requests found</p>
                                <a href="{{ route('staff.location-requests.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Submit First Request
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
function filterRequests(status) {
    const rows = document.querySelectorAll('#requestsTable tbody tr');
    
    rows.forEach(row => {
        if (!status || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endpush
