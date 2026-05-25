@extends('staff.layouts.app')
@section('title', 'Recommended Beneficiaries')

@section('content')
<div class="dash-header">
    <h1>Recommended Beneficiaries</h1>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- FILTERS --}}
<div class="filters-card">
    <form method="GET" action="{{ route('staff.recommended.index') }}" id="filterForm" class="filters-form">
        <div class="filter-group">
            <label class="filter-label">Barangay</label>
            <select name="barangay_id" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">All Barangays</option>
                @foreach($barangays as $b)
                    <option value="{{ $b->id }}" {{ $barangayId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">Status</label>
            <select name="status" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">All</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>

        <div class="filter-actions">
            <a href="{{ route('staff.recommended.index') }}" class="btn-filter-reset">
                <i class="fas fa-redo"></i> Reset
            </a>
        </div>
    </form>
</div>

<div class="table-container">
    <div class="table-wrapper">
        <table class="responsive-table">
            <thead>
                <tr>
                    <th data-priority="1">#</th>
                    <th data-priority="1">Name</th>
                    <th data-priority="2">Barangay</th>
                    <th data-priority="3">Contact</th>
                    <th data-priority="4">Address</th>
                    <th data-priority="5">Submitted by</th>
                    <th data-priority="2">Status</th>
                    <th data-priority="1">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recommended as $i => $r)
                <tr>
                    <td data-label="#">{{ $recommended->firstItem() + $i }}</td>
                    <td data-label="Name">
                        <div class="name-cell">
                            <strong>{{ $r->first_name }} {{ $r->middle_name ? $r->middle_name . ' ' : '' }}{{ $r->last_name }}{{ $r->suffix ? ', ' . $r->suffix : '' }}</strong>
                        </div>
                    </td>
                    <td data-label="Barangay">{{ $r->barangay->name }}</td>
                    <td data-label="Contact">
                        <div class="contact-cell">
                            @if($r->contact_number)
                                <a href="tel:{{ $r->contact_number }}" class="contact-link">
                                    {{ $r->contact_number }}
                                </a>
                            @else
                                <span class="no-data">N/A</span>
                            @endif
                        </div>
                    </td>
                    <td data-label="Address">
                        <div class="address-cell">
                            {{ $r->address ?? 'N/A' }}
                        </div>
                    </td>
                    <td data-label="Submitted by">{{ $r->submittedBy->first_name ?? 'N/A' }}</td>
                    <td data-label="Status">
                        @if($r->status === 'Pending')
                            <span class="status-badge pending">Pending</span>
                        @elseif($r->status === 'Converted')
                            <span class="status-badge converted">Converted</span>
                        @else
                            <span class="status-badge rejected">Rejected</span>
                        @endif
                    </td>
                    <td data-label="Actions">
                        <div class="action-buttons">
                            @if($r->status === 'Pending')
                            <a href="{{ route('staff.recommended.convert', $r->id) }}"
                                class="btn-action btn-interview">
                                <i class="fas fa-user-check"></i>
                                <span>Interview</span>
                            </a>
                            <form method="POST" action="{{ route('staff.recommended.reject', $r->id) }}"
                                onsubmit="return confirm('Reject this recommendation?')">
                                @csrf
                                <button type="submit" class="btn-action btn-reject">
                                    <i class="fas fa-times"></i>
                                    <span>Reject</span>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-cell">
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <h3>No recommendations yet</h3>
                            <p>Start by adding beneficiary recommendations</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    <div class="pagination-container">
        {{ $recommended->withQueryString()->links() }}
    </div>
</div>

@push('styles')
<style>
/* ============================================
   RESPONSIVE TABLE STYLES
   ============================================ */
.table-container {
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden;
}

.table-wrapper {
    overflow-x: auto;
    overflow-y: auto;
    max-height: 70vh;
    -webkit-overflow-scrolling: touch;
}

.responsive-table {
    width: 100%;
    min-width: 800px;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.responsive-table th {
    background: #f9fafb;
    color: #374151;
    font-weight: 600;
    text-align: left;
    padding: 0.75rem 1rem;
    border-bottom: 2px solid #e5e7eb;
    position: sticky;
    top: 0;
    z-index: 10;
    white-space: nowrap;
}

.responsive-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: top;
}

/* Cell Content Styling */
.name-cell strong {
    color: #1f2937;
    font-size: 0.9rem;
}

.contact-cell {
    font-size: 0.85rem;
}

.contact-link {
    color: #059669;
    text-decoration: none;
    font-weight: 500;
}

.contact-link:hover {
    text-decoration: underline;
}

.no-data {
    color: #9ca3af;
    font-style: italic;
}

.address-cell {
    max-width: 200px;
    word-wrap: break-word;
    line-height: 1.4;
}

/* Status Badges */
.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.pending {
    background: #fef3c7;
    color: #d97706;
}

.status-badge.converted {
    background: #d1fae5;
    color: #059669;
}

.status-badge.rejected {
    background: #fee2e2;
    color: #dc2626;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}

.btn-action i {
    font-size: 0.7rem;
}

.btn-interview {
    background: #10b981;
    color: white;
}

.btn-interview:hover {
    background: #059669;
    transform: translateY(-1px);
}

.btn-reject {
    background: #ef4444;
    color: white;
}

.btn-reject:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

/* Empty State */
.empty-cell {
    padding: 0 !important;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #6b7280;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #d1d5db;
}

.empty-state h3 {
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0 0 0.5rem;
    color: #374151;
}

.empty-state p {
    font-size: 0.875rem;
    margin: 0;
}

/* Modern Filters */
.filters-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.filters-form {
    display: flex;
    align-items: flex-end;
    gap: 1rem;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    min-width: 150px;
}

.filter-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-select {
    padding: 0.5rem 2rem 0.5rem 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.875rem;
    background: white url('data:image/svg+xml;utf8,<svg fill="%236b7280" height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>') no-repeat;
    background-position: right 0.6rem center;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
}

.filter-select:hover {
    border-color: #1a6b2a;
    background-color: #fff;
}

.filter-select:focus {
    outline: none;
    border-color: #1a6b2a;
    box-shadow: 0 0 0 3px rgba(26, 107, 42, 0.1);
    background-color: #fff;
}

.filter-select:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.filter-actions {
    display: flex;
    gap: 0.5rem;
    align-items: flex-end;
}

.btn-filter-reset {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #6b7280;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-filter-reset:hover {
    background: #4b5563;
    transform: translateY(-1px);
}

/* Pagination */
.pagination-container {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
    background: #f9fafb;
}

/* ============================================
   RESPONSIVE BREAKPOINTS
   ============================================ */

/* Tablet */
@media (max-width: 1024px) {
    .table-wrapper {
        max-height: 60vh;
    }
    
    .responsive-table {
        min-width: 700px;
    }
}

/* Mobile Landscape */
@media (max-width: 768px) {
    .table-wrapper {
        max-height: 50vh;
    }
    
    .responsive-table {
        min-width: 600px;
    }
    
    .btn-action span {
        display: none;
    }
    
    .btn-action {
        padding: 0.5rem;
        min-width: 40px;
        justify-content: center;
    }
}

/* Mobile Portrait */
@media (max-width: 480px) {
    .table-wrapper {
        max-height: 40vh;
    }
    
    .responsive-table {
        min-width: 500px;
    }
    
    .responsive-table th,
    .responsive-table td {
        padding: 0.5rem 0.75rem;
    }
    
    .address-cell {
        max-width: 150px;
    }
    
    /* Filter Responsive */
    .filters-form {
        flex-direction: column;
        align-items: flex-start;
    }

    .filters-card {
        padding: 0.75rem;
    }
    
    .filter-group {
        min-width: calc(50% - 0.5rem);
        flex: none;
    }
    
    .filter-actions {
        width: 100%;
        justify-content: flex-start;
        margin-top: 0.25rem;
    }
    
    .btn-filter-reset {
        width: auto;
    }
}

/* Small Mobile */
@media (max-width: 360px) {
    .table-wrapper {
        max-height: 35vh;
    }
    
    .responsive-table {
        min-width: 450px;
    }
    
    .responsive-table th,
    .responsive-table td {
        padding: 0.375rem 0.5rem;
        font-size: 0.8rem;
    }
    
    .status-badge {
        padding: 0.2rem 0.5rem;
        font-size: 0.65rem;
    }

    .filter-group {
        min-width: 100%;
    }
    
    .filter-select {
        font-size: 0.8rem;
    }
}

/* ============================================
   ALERT STYLES
   ============================================ */
.alert {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1.25rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.alert-success {
    background: #d1fae5;
    color: #059669;
    border: 1px solid #bbf7d0;
}
</style>
@endpush

@endsection