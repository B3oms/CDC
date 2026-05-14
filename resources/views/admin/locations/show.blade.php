@extends('admin.layouts.app')
@section('title', 'Location Details')

@section('content')
<div class="location-detail-header">
    <div class="location-breadcrumb">
        <a href="{{ route('admin.locations.index') }}" class="breadcrumb-link">
            <i class="fas fa-arrow-left"></i> Back to Locations
        </a>
    </div>
    <div class="location-detail-content">
        <div class="location-detail-info">
            <h1 class="location-detail-title">{{ $locationRequest->name }}</h1>
            <p class="location-detail-subtitle">{{ ucfirst($locationRequest->type) }}</p>
        </div>
        <div class="location-detail-status">
            @if($locationRequest->status === 'pending')
                <span class="status-badge pending">
                    <i class="fas fa-clock"></i>
                    Pending
                </span>
            @elseif($locationRequest->status === 'approved')
                <span class="status-badge approved">
                    <i class="fas fa-check-circle"></i>
                    Approved
                </span>
            @elseif($locationRequest->status === 'rejected')
                <span class="status-badge rejected">
                    <i class="fas fa-times-circle"></i>
                    Rejected
                </span>
            @endif
        </div>
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
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<div class="location-detail-card">
    <div class="location-detail-header-info">
        <div class="location-detail-main">
            <h2 class="location-name">{{ $locationRequest->name }}</h2>
            <p class="location-type">{{ ucfirst($locationRequest->type) }}</p>
        </div>
        <div class="location-detail-status-badge">
            @if($locationRequest->status === 'pending')
                <span class="status-badge pending">
                    <i class="fas fa-clock"></i>
                    Pending
                </span>
            @elseif($locationRequest->status === 'approved')
                <span class="status-badge approved">
                    <i class="fas fa-check-circle"></i>
                    Approved
                </span>
            @elseif($locationRequest->status === 'rejected')
                <span class="status-badge rejected">
                    <i class="fas fa-times-circle"></i>
                    Rejected
                </span>
            @endif
        </div>
    </div>

    <div class="location-detail-grid">
        <div class="detail-section">
            <h3 class="detail-section-title">
                <i class="fas fa-info-circle"></i>
                Request Information
            </h3>
            <div class="detail-list">
                <div class="detail-item">
                    <span class="detail-label">Type</span>
                    <span class="detail-value">{{ ucfirst($locationRequest->type) }}</span>
                </div>
                @if($locationRequest->type === 'municipality')
                    <div class="detail-item">
                        <span class="detail-label">Region</span>
                        <span class="detail-value">{{ $locationRequest->region }}</span>
                    </div>
                @elseif($locationRequest->type === 'barangay')
                    <div class="detail-item">
                        <span class="detail-label">Municipality</span>
                        <span class="detail-value">{{ $locationRequest->municipality_id }}</span>
                    </div>
                @endif
                <div class="detail-item">
                    <span class="detail-label">Submitted</span>
                    <span class="detail-value">{{ date('M d, Y h:i A', strtotime($locationRequest->created_at)) }}</span>
                </div>
            </div>
        </div>

        <div class="detail-section">
            <h3 class="detail-section-title">
                <i class="fas fa-user"></i>
                User Information
            </h3>
            <div class="detail-list">
                <div class="detail-item">
                    <span class="detail-label">Submitted By</span>
                    <span class="detail-value">{{ $locationRequest->requested_by_firstname }} {{ $locationRequest->requested_by_lastname }}</span>
                </div>
                @if($locationRequest->status === 'approved')
                    <div class="detail-item">
                        <span class="detail-label">Approved By</span>
                        <span class="detail-value">{{ $locationRequest->approved_by_firstname }} {{ $locationRequest->approved_by_lastname }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Approved At</span>
                        <span class="detail-value">{{ date('M d, Y h:i A', strtotime($locationRequest->approved_at)) }}</span>
                    </div>
                @endif
                @if($locationRequest->status === 'rejected')
                    <div class="detail-item">
                        <span class="detail-label">Rejected By</span>
                        <span class="detail-value">{{ $locationRequest->approved_by_firstname }} {{ $locationRequest->approved_by_lastname }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Rejected At:</span>
                        <span class="ml-2">{{ date('M d, Y h:i A', strtotime($locationRequest->approved_at)) }}</span>
                    </div>
                    @if($locationRequest->rejection_reason)
                        <div>
                            <span class="text-gray-500">Reason:</span>
                            <span class="ml-2 text-red-600">{{ $locationRequest->rejection_reason }}</span>
                        </div>
                    @endif
                <div class="detail-item">
                        <span class="detail-label">Rejected At</span>
                        <span class="detail-value">{{ date('M d, Y h:i A', strtotime($locationRequest->approved_at)) }}</span>
                    </div>
                    @if($locationRequest->rejection_reason)
                        <div class="detail-item">
                            <span class="detail-label">Reason</span>
                            <span class="detail-value rejection-reason">{{ $locationRequest->rejection_reason }}</span>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    @if($locationRequest->status === 'pending')
    <div class="location-detail-actions">
        <div class="actions-header">
            <h3 class="actions-title">Actions</h3>
        </div>
        <div class="actions-grid">
            <form method="POST" action="{{ route('admin.locations.approve', $locationRequest->id) }}" class="action-form">
                @csrf
                <button type="submit" class="btn btn-approve btn-large" onclick="return confirm('Approve this request?')">
                    <i class="fas fa-check"></i>
                    Approve Request
                </button>
            </form>

            <form method="POST" action="{{ route('admin.locations.reject', $locationRequest->id) }}" 
                  onsubmit="return validateReject(this)" class="action-form">
            @csrf
            <div class="reject-form">
                <textarea 
                    name="rejection_reason" 
                    class="form-input" 
                    placeholder="Reason for rejection (required)"
                    rows="1"
                ></textarea>
                <button type="submit" class="btn btn-reject btn-large">
                    <i class="fas fa-times"></i>
                    Reject Request
                </button>
            </div>
        </form>
    </div>
    @endif

    @if($locationRequest->status === 'rejected')
        <div class="location-detail-actions">
            <div class="actions-header">
                <h3 class="actions-title">Actions</h3>
            </div>
            <div class="actions-grid">
                <form method="POST" action="{{ route('admin.locations.restore', $locationRequest->id) }}" class="action-form">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-large" onclick="return confirm('Restore this request?')">
                        <i class="fas fa-undo"></i>
                        Restore Request
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
/* Modern Location Detail Styles */
.location-detail-header {
    margin-bottom: 2rem;
}

.location-breadcrumb {
    margin-bottom: 1.5rem;
}

.breadcrumb-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
    text-decoration: none;
    font-size: 0.875rem;
    transition: color 0.2s ease;
}

.breadcrumb-link:hover {
    color: #1a3d1f;
}

.location-detail-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
}

.location-detail-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1a3d1f;
    margin: 0 0 0.5rem 0;
}

.location-detail-subtitle {
    color: #6b7280;
    font-size: 1rem;
    margin: 0;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}

.status-badge.pending {
    background: #fef3c7;
    color: #d97706;
}

.status-badge.approved {
    background: #d1fae5;
    color: #059669;
}

.status-badge.rejected {
    background: #fee2e2;
    color: #dc2626;
}

.location-detail-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.location-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.detail-section {
    background: #f9fafb;
    border-radius: 8px;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
}

.detail-section-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 1rem 0;
}

.detail-section-title i {
    color: #1a3d1f;
}

.detail-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
}

.detail-label {
    font-weight: 500;
    color: #6b7280;
    font-size: 0.875rem;
}

.detail-value {
    font-weight: 500;
    color: #1f2937;
    font-size: 0.875rem;
}

.rejection-reason {
    color: #dc2626;
}

.location-detail-actions {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.actions-header {
    margin-bottom: 1rem;
}

.actions-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.actions-grid {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.action-form {
    display: inline;
}

.reject-form {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.reject-form .form-input {
    flex: 1;
    min-width: 250px;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.875rem;
    resize: vertical;
}

.btn-large {
    padding: 0.75rem 1.5rem;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .location-detail-content {
        flex-direction: column;
        gap: 1rem;
    }
    
    .location-detail-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .actions-grid {
        flex-direction: column;
    }
    
    .reject-form {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn-large {
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
function validateReject(form) {
    const reason = form.querySelector('[name="rejection_reason"]').value.trim();
    if (!reason) {
        alert('Please provide a rejection reason.');
        return false;
    }
    return confirm('Reject this request?');
}
</script>
@endpush

@endsection
