@extends('admin.layouts.app')
@section('title', 'Household Requests')
@section('breadcrumb', 'Household Requests')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/barangay-system.css') }}">
@endpush

@section('content')
<div class="household-requests-page">
    <div class="page-header">
        <div>
            <h1>Household Requests</h1>
            <p class="page-sub">Manage household assistance requests</p>
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

<div class="household-grid">
    <div class="requests-section">
        <div class="form-card">
            <div class="form-card-header">
                <span class="form-card-icon info"><i class="fas fa-clipboard-list"></i></span>
                <h3>Your Household Requests</h3>
            </div>
            @if($requests->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Requests Yet</h3>
                    <p>No household requests submitted yet.</p>
                    <a href="{{ route('barangay.household_requests.create') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Submit First Request
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="modern-table">
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
                                    @if($request->status === 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($request->status === 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($request->status === 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($request->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $request->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('barangay.household_requests.show', $request->id) }}" class="btn-secondary">
                                        View
                                    </a>
                                    @if($request->isPending())
                                        <a href="{{ route('barangay.household_requests.edit', $request->id) }}" class="btn-primary" style="margin-left: 5px;">
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
    </div>

    <div class="households-section">
        <div class="form-card">
            <div class="form-card-header">
                <span class="form-card-icon green"><i class="fas fa-home"></i></span>
                <h3>Your Households</h3>
            </div>
            @if($households->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-home"></i>
                    <h3>No Households</h3>
                    <p>No approved households found for your barangay.</p>
                </div>
            @else
                <div class="households-stats">
                    <div class="stat-item">
                        <span class="stat-value">{{ $households->count() }}</span>
                        <span class="stat-desc">Total Households</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ $households->sum(function($h) { return $h->members->count() + 1; }) }}</span>
                        <span class="stat-desc">Total Members</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Head of Household</th>
                                <th>Address</th>
                                <th>Family Members</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($households as $household)
                            <tr>
                                <td>{{ $household->head_of_household }}</td>
                                <td>{{ $household->address }}</td>
                                <td>{{ $household->members->count() + 1 }} members</td>
                                <td>
                                    <span class="badge badge-success">Approved</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection

@push('styles')
<style>
/* Household Requests Page Specific Styles */
.household-requests-page {
    max-width: 100%;
    padding: 0;
}

.household-grid {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    align-items: stretch;
}

.requests-section {
    min-height: 0;
}

.households-section {
    min-height: 0;
}

/* Form Card Fixes */
.form-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.form-card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f3f4f6;
}

.form-card-header h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: #1f2937;
}

.form-card-icon {
    width: 38px;
    height: 38px;
    background: #f3f4f6;
    color: #6b7280;
    border: 1px solid #e5e7eb;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.form-card-icon.green {
    background: #eaf3de;
    color: #1a6b2a;
    border-color: #1a6b2a;
}

.form-card-icon.info {
    background: #eff6ff;
    color: #2563eb;
    border-color: #2563eb;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 2rem 1rem;
    color: #6b7280;
}

.empty-state i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    display: block;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #4b5563;
    margin: 0 0 0.5rem;
}

.empty-state p {
    font-size: 0.875rem;
    margin: 0 0 1rem;
}

/* Table Fixes */
.modern-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.825rem;
}

.modern-table thead {
    background: #f8faf8;
    border-bottom: 2px solid #e5e7eb;
}

.modern-table th {
    padding: 0.75rem 0.75rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b7280;
    white-space: nowrap;
}

.modern-table td {
    padding: 0.7rem 0.75rem;
    border-bottom: 1px solid #f3f4f6;
    color: #374151;
    vertical-align: middle;
}

.modern-table tbody tr:last-child td {
    border-bottom: none;
}

/* Badge Fixes */
.badge {
    display: inline-block;
    padding: 0.2rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.badge-success {
    background: #ecfdf5;
    color: #059669;
}

.badge-warning {
    background: #fffbeb;
    color: #d97706;
}

.badge-danger {
    background: #fef2f2;
    color: #dc2626;
}

.badge-secondary {
    background: #f3f4f6;
    color: #6b7280;
}

/* Households Stats */
.households-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.households-stats .stat-item {
    background: #eaf3de;
    border: 1px solid #1a6b2a;
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
}

.households-stats .stat-value {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a6b2a;
    margin-bottom: 0.25rem;
}

.households-stats .stat-desc {
    display: block;
    font-size: 0.75rem;
    color: #1a3d1f;
    font-weight: 500;
}

/* Responsive */
@media (max-width: 1024px) {
    .household-grid {
        gap: 1.25rem;
    }
    
    .households-stats {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
}
</style>
@endpush
