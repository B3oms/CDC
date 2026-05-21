@extends('admin.layouts.app')
@section('title', 'Location Details')

@section('content')
<div class="dash-header">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
        <x-back-button href="{{ route('admin.locations.index') }}" label="Back to Locations" />
    </div>
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #2c2c2a; margin: 0;">{{ $locationRequest->name }}</h1>
    <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">{{ ucfirst($locationRequest->type) }}</p>
    <div style="margin-top: 1rem;">
        @if($locationRequest->status === 'pending')
            <span class="status-badge pending" style="background: #fef3c7; color: #d97706; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500;">
                <i class="fas fa-clock"></i> Pending
            </span>
        @elseif($locationRequest->status === 'approved')
            <span class="status-badge approved" style="background: #d1fae5; color: #059669; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500;">
                <i class="fas fa-check-circle"></i> Approved
            </span>
        @elseif($locationRequest->status === 'rejected')
            <span class="status-badge rejected" style="background: #fee2e2; color: #dc2626; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500;">
                <i class="fas fa-times-circle"></i> Rejected
            </span>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:1.5rem;">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error" style="margin-bottom:1.5rem;">
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
                  onsubmit="return validateReject(this)" class="action-form reject-action-form">
                @csrf
                <div class="reject-form">
                    <textarea
                        name="rejection_reason"
                        class="form-input"
                        placeholder="Reason for rejection (required)"
                        rows="2"
                    ></textarea>
                    <button type="submit" class="btn btn-reject btn-large">
                        <i class="fas fa-times"></i>
                        Reject Request
                    </button>
                </div>
            </form>
        </div>
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
/* =============================================
   CSS VARIABLES
   ============================================= */
:root {
    --color-primary:      #1a3d1f;
    --color-primary-light:#2d6a35;
    --color-gray-50:      #f9fafb;
    --color-gray-100:     #f3f4f6;
    --color-gray-200:     #e5e7eb;
    --color-gray-400:     #9ca3af;
    --color-gray-500:     #6b7280;
    --color-gray-700:     #374151;
    --color-gray-900:     #111827;
    --color-white:        #ffffff;

    --color-pending-bg:   #fef3c7;
    --color-pending-text: #d97706;
    --color-approved-bg:  #d1fae5;
    --color-approved-text:#059669;
    --color-rejected-bg:  #fee2e2;
    --color-rejected-text:#dc2626;

    --radius-sm:  6px;
    --radius-md:  8px;
    --radius-lg:  12px;
    --radius-pill:20px;

    --shadow-sm: 0 1px 3px rgba(0,0,0,.08);
    --shadow-md: 0 4px 12px rgba(0,0,0,.10);

    --space-xs:  0.25rem;
    --space-sm:  0.5rem;
    --space-md:  1rem;
    --space-lg:  1.5rem;
    --space-xl:  2rem;

    --font-sm:   0.813rem;
    --font-base: 0.875rem;
    --font-md:   1rem;
    --font-lg:   1.125rem;
    --font-xl:   1.5rem;
    --font-2xl:  2rem;
}

/* =============================================
   ALERTS
   ============================================= */
.alert {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    padding: var(--space-md) var(--space-lg);
    border-radius: var(--radius-md);
    margin-bottom: var(--space-lg);
    font-size: var(--font-base);
    font-weight: 500;
}

.alert-success {
    background: var(--color-approved-bg);
    color: var(--color-approved-text);
}

.alert-error {
    background: var(--color-rejected-bg);
    color: var(--color-rejected-text);
}

/* =============================================
   PAGE HEADER
   ============================================= */
.location-detail-header {
    margin-bottom: var(--space-xl);
}

.location-breadcrumb {
    margin-bottom: var(--space-lg);
}

.breadcrumb-link {
    display: inline-flex;
    align-items: center;
    gap: var(--space-sm);
    color: var(--color-gray-500);
    text-decoration: none;
    font-size: var(--font-base);
    transition: color 0.2s ease;
}

.breadcrumb-link:hover {
    color: var(--color-primary);
}

.location-detail-content {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: var(--space-md);
}

.location-detail-title {
    font-size: clamp(1.375rem, 4vw, var(--font-2xl));
    font-weight: 700;
    color: var(--color-primary);
    margin: 0 0 var(--space-xs) 0;
    line-height: 1.2;
    word-break: break-word;
}

.location-detail-subtitle {
    color: var(--color-gray-500);
    font-size: var(--font-md);
    margin: 0;
}

/* =============================================
   STATUS BADGE
   ============================================= */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: var(--space-sm);
    padding: var(--space-sm) var(--space-md);
    border-radius: var(--radius-pill);
    font-size: var(--font-base);
    font-weight: 500;
    white-space: nowrap;
}

.status-badge.pending  { background: var(--color-pending-bg);  color: var(--color-pending-text);  }
.status-badge.approved { background: var(--color-approved-bg); color: var(--color-approved-text); }
.status-badge.rejected { background: var(--color-rejected-bg); color: var(--color-rejected-text); }

/* =============================================
   MAIN CARD
   ============================================= */
.location-detail-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: clamp(1rem, 4vw, var(--space-xl));
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--color-gray-200);
}

/* Card inner header */
.location-detail-header-info {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: var(--space-md);
    margin-bottom: var(--space-xl);
    padding-bottom: var(--space-lg);
    border-bottom: 1px solid var(--color-gray-200);
}

.location-name {
    font-size: clamp(1.125rem, 3vw, 1.5rem);
    font-weight: 700;
    color: var(--color-primary);
    margin: 0 0 var(--space-xs) 0;
    word-break: break-word;
}

.location-type {
    color: var(--color-gray-500);
    font-size: var(--font-base);
    margin: 0;
}

/* =============================================
   DETAIL GRID — 2-col on md+, 1-col on mobile
   ============================================= */
.location-detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr));
    gap: var(--space-lg);
    margin-bottom: var(--space-xl);
}

.detail-section {
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
    padding: var(--space-lg);
    border: 1px solid var(--color-gray-200);
}

.detail-section-title {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    font-size: var(--font-lg);
    font-weight: 600;
    color: var(--color-gray-900);
    margin: 0 0 var(--space-md) 0;
}

.detail-section-title i {
    color: var(--color-primary);
    flex-shrink: 0;
}

.detail-list {
    display: flex;
    flex-direction: column;
    gap: var(--space-sm);
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: var(--space-sm);
    padding: var(--space-sm) 0;
    border-bottom: 1px solid var(--color-gray-200);
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 500;
    color: var(--color-gray-500);
    font-size: var(--font-sm);
    flex-shrink: 0;
    padding-top: 1px;
}

.detail-value {
    font-weight: 500;
    color: var(--color-gray-700);
    font-size: var(--font-sm);
    text-align: right;
    word-break: break-word;
}

.rejection-reason {
    color: var(--color-rejected-text);
}

/* =============================================
   ACTIONS SECTION
   ============================================= */
.location-detail-actions {
    background: var(--color-gray-50);
    border-radius: var(--radius-lg);
    padding: var(--space-lg);
    border: 1px solid var(--color-gray-200);
}

.actions-header {
    margin-bottom: var(--space-md);
}

.actions-title {
    font-size: var(--font-lg);
    font-weight: 600;
    color: var(--color-gray-900);
    margin: 0;
}

.actions-grid {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-md);
    align-items: flex-start;
}

/* Approve form keeps natural width on large screens */
.action-form {
    display: contents; /* children participate in flex layout directly */
}

/* Reject form stretches to fill remaining space */
.reject-action-form {
    display: contents;
}

.reject-form {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-md);
    align-items: flex-start;
    flex: 1 1 280px;
}

.reject-form .form-input {
    flex: 1 1 200px;
    min-width: 0;
    padding: 0.625rem 0.75rem;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    font-size: var(--font-base);
    resize: vertical;
    font-family: inherit;
    color: var(--color-gray-700);
    background: var(--color-white);
    transition: border-color 0.2s;
}

.reject-form .form-input:focus {
    outline: none;
    border-color: var(--color-primary-light);
    box-shadow: 0 0 0 3px rgba(26,61,31,.1);
}

/* =============================================
   BUTTONS
   ============================================= */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-sm);
    border: none;
    border-radius: var(--radius-md);
    cursor: pointer;
    font-family: inherit;
    font-weight: 600;
    text-decoration: none;
    transition: opacity 0.2s, transform 0.15s;
    white-space: nowrap;
    -webkit-tap-highlight-color: transparent;
}

.btn:active {
    transform: scale(0.97);
}

.btn-large {
    padding: 0.625rem 1.25rem;
    font-size: var(--font-base);
    min-height: 42px;
}

.btn-approve {
    background: var(--color-approved-text);
    color: var(--color-white);
    flex-shrink: 0;
}

.btn-approve:hover { opacity: 0.88; }

.btn-reject {
    background: var(--color-rejected-text);
    color: var(--color-white);
    flex-shrink: 0;
}

.btn-reject:hover { opacity: 0.88; }

.btn-outline {
    background: transparent;
    color: var(--color-primary);
    border: 2px solid var(--color-primary);
}

.btn-outline:hover {
    background: var(--color-primary);
    color: var(--color-white);
}

/* =============================================
   RESPONSIVE BREAKPOINTS
   ============================================= */

/* ---- Tablet (≥ 600 px) ---- */
@media (min-width: 600px) {
    .detail-item {
        align-items: center;
    }
}

/* ---- Small phones (< 400 px) ---- */
@media (max-width: 399px) {
    .location-detail-card {
        padding: var(--space-md);
    }

    .detail-section {
        padding: var(--space-md);
    }

    .location-detail-actions {
        padding: var(--space-md);
    }

    .actions-grid {
        flex-direction: column;
    }

    .btn-large {
        width: 100%;
    }

    .reject-form {
        flex-direction: column;
    }

    .reject-form .form-input {
        width: 100%;
    }
}

/* ---- General mobile (< 600 px) ---- */
@media (max-width: 599px) {
    .location-detail-content {
        flex-direction: column;
        gap: var(--space-sm);
    }

    .location-detail-header-info {
        flex-direction: column;
        gap: var(--space-sm);
    }

    .detail-value {
        text-align: left;
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