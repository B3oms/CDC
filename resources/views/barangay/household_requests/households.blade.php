@extends('admin.layouts.app')
@section('title', 'Households')

@section('content')
<div class="page-container">
    <div class="dash-header">
        <div class="header-content">
            <div class="header-text">
                <h1>Households</h1>
                <p class="sub">View all registered households in your barangay</p>
            </div>
        </div>
    </div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

{{-- FILTERS --}}
<div class="filters-card">
    <form method="GET" action="{{ route('barangay.household_requests.households') }}" class="filters-form">
        <div class="filter-group">
            <label class="filter-label">Family Size</label>
            <select name="family_size" class="filter-select" onchange="this.form.submit()">
                <option value="">All Family Sizes</option>
                @for($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}" {{ request('family_size') == $i ? 'selected' : '' }}>
                        {{ $i }} members
                    </option>
                @endfor
            </select>
        </div>

        <div class="filter-actions">
            <a href="{{ route('barangay.household_requests.households') }}" class="btn-filter-reset">
                <i class="fas fa-redo"></i> Reset
            </a>
        </div>
    </form>
</div>

{{-- HOUSEHOLDS TABLE --}}
<div class="section-card" style="margin-top:1rem;">
    <div class="table-container">
        <table class="dist-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Head of Household</th>
                    <th>Contact</th>
                    <th>Family Size</th>
                    <th>Address</th>
                    <th>Approved</th>
                    <th class="actions-column">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($households as $i => $household)
                <tr>
                    <td>{{ $households->firstItem() + $i }}</td>
                    <td>
                        <div class="household-info">
                            <strong>{{ $household->head_of_household }}</strong>
                            @if($household->head_age)
                                <br><small class="household-details">{{ $household->head_age }} years, {{ ucfirst($household->head_sex) }}</small>
                            @endif
                        </div>
                    </td>
                    <td class="contact-cell">{{ $household->formatted_contact_number ?? 'N/A' }}</td>
                    <td>
                        <span class="family-size-badge">
                            {{ $household->family_size }} members
                        </span>
                    </td>
                    <td class="address-cell">{{ $household->address }}</td>
                    <td>{{ $household->approved_at ? $household->approved_at->format('M d, Y') : 'N/A' }}</td>
                    <td class="actions-cell">
                        <div class="action-buttons">
                            <a href="{{ route('barangay.household_requests.show', $household->id) }}"
                                class="btn-sm-secondary">
                                <i class="fas fa-eye"></i>View
                            </a>
                            <a href="{{ route('barangay.household_requests.edit', $household->id) }}"
                                class="btn-sm-primary">
                                <i class="fas fa-edit"></i>Edit
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="no-data-row">
                        No households found in your barangay.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        {{ $households->withQueryString()->links() }}
    </div>
</div>

<style>
/* Page Container and Layout */
.page-container {
    max-width: 100%;
    margin: 0 auto;
    padding: 0 1rem;
    min-height: 100vh;
}

.dash-header {
    margin-bottom: 1.5rem;
    padding: 1rem 0;
}

.header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-text h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
    line-height: 1.2;
}

.header-text .sub {
    color: #6b7280;
    font-size: 1rem;
    margin: 0;
    line-height: 1.5;
}

/* Alert Success */
.alert-success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    border: 1px solid #10b981;
    color: #065f46;
    padding: 1rem 1.25rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.1);
}

/* Responsive Table Container */
.table-container {
    overflow-x: auto;
    overflow-y: auto;
    max-height: 70vh;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: white;
}

/* Table Styling */
.dist-table {
    width: 100%;
    min-width: 800px;
    border-collapse: collapse;
    font-size: 14px;
}

.dist-table th {
    background: #f8faf9;
    padding: 12px 8px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 10;
}

.dist-table td {
    padding: 12px 8px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.dist-table tr:hover {
    background: #f9fafb;
}

/* Column Specific Styling */
.actions-column {
    width: 120px;
    text-align: center;
}

.household-info {
    min-width: 150px;
}

.household-details {
    color: #6b7280;
    font-size: 11px;
}

.contact-cell {
    min-width: 120px;
    word-break: break-word;
}

.address-cell {
    min-width: 200px;
    max-width: 250px;
    word-break: break-word;
}

.family-size-badge {
    background: #e5e7eb;
    color: #374151;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    display: inline-block;
}

.no-data-row {
    text-align: center;
    color: #888;
    padding: 40px 20px;
    font-style: italic;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 6px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-sm-secondary {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    border: 1px solid #6b7280;
    color: white;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(107, 114, 128, 0.1);
}

.btn-sm-primary {
    background: linear-gradient(135deg, #1a3d1f, #2d5a31);
    border: 1px solid #1a3d1f;
    color: white;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(26, 61, 31, 0.1);
}

.btn-sm-secondary:hover {
    background: linear-gradient(135deg, #4b5563, #6b7280) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(107, 114, 128, 0.2) !important;
}

.btn-sm-secondary:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(107, 114, 128, 0.1) !important;
}

.btn-sm-primary:hover {
    background: linear-gradient(135deg, #2d5a31, #1a3d1f) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(26, 61, 31, 0.2) !important;
}

.btn-sm-primary:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(26, 61, 31, 0.1) !important;
}

/* Pagination */
.pagination-container {
    margin-top: 1rem;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
}

/* Modern Filters - Copied from Beneficiaries */
.filters-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.filters-form {
    display: flex;
    gap: 1rem;
    align-items: flex-end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.filter-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    letter-spacing: 0.5px;
}

.filter-select {
    padding: 0.5rem 2rem 0.5rem 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.875rem;
    color: #374151;
    background-color: #fff;
    transition: all 0.2s ease;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.6rem center;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

.filter-select:hover {
    border-color: #1a6b2a;
    background-color: #fff;
}

.filter-select:focus {
    outline: none;
    border-color: #1a6b2a;
    box-shadow: 0 0 0 3px rgba(26, 107, 42, 0.1);
}

.filter-actions {
    display: flex;
    align-items: flex-end;
}

.btn-filter-reset {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.5rem 1rem;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    text-decoration: none;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn-filter-reset:hover {
    background: #e5e7eb;
    transform: translateY(-1px);
}

/* Responsive Breakpoints */
/* Comprehensive Responsive Breakpoints */

/* Large Desktop (1200px and below) */
@media (max-width: 1200px) {
    .page-container {
        padding: 0 0.75rem;
    }
    
    .dash-header {
        margin-bottom: 1.25rem;
        padding: 0.75rem 0;
    }
    
    .header-text h1 {
        font-size: 1.75rem;
    }
    
    .header-text .sub {
        font-size: 0.95rem;
    }
    
    .table-container {
        max-height: 60vh;
    }
    
    .dist-table {
        min-width: 700px;
        font-size: 13px;
    }
    
    .dist-table th,
    .dist-table td {
        padding: 10px 6px;
    }
    
    .filters-card {
        padding: 0.875rem;
    }
}

/* Tablet (768px and below) */
@media (max-width: 768px) {
    .page-container {
        padding: 0 0.5rem;
    }
    
    .dash-header {
        margin-bottom: 1rem;
        padding: 0.5rem 0;
    }
    
    .header-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .header-text h1 {
        font-size: 1.5rem;
    }
    
    .header-text .sub {
        font-size: 0.875rem;
    }
    
    .alert-success {
        padding: 0.875rem 1rem;
        font-size: 0.875rem;
    }
    
    .table-container {
        max-height: 50vh;
    }
    
    .dist-table {
        min-width: 600px;
        font-size: 12px;
    }
    
    .dist-table th,
    .dist-table td {
        padding: 8px 4px;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 4px;
    }
    
    .btn-sm-secondary,
    .btn-sm-primary {
        padding: 4px 8px;
        font-size: 10px;
    }
    
    /* Responsive Filter */
    .filters-form {
        flex-direction: column;
        align-items: stretch;
        gap: 0.75rem;
    }
    
    .filter-group {
        min-width: 120px;
    }
}

/* Mobile (480px and below) */
@media (max-width: 480px) {
    .page-container {
        padding: 0 0.25rem;
    }
    
    .dash-header {
        margin-bottom: 0.75rem;
        padding: 0.25rem 0;
    }
    
    .header-text h1 {
        font-size: 1.25rem;
    }
    
    .header-text .sub {
        font-size: 0.8rem;
    }
    
    .alert-success {
        padding: 0.75rem 0.875rem;
        font-size: 0.8rem;
        margin-bottom: 1rem;
    }
    
    .table-container {
        max-height: 40vh;
    }
    
    .dist-table {
        min-width: 500px;
        font-size: 11px;
    }
    
    .dist-table th,
    .dist-table td {
        padding: 6px 3px;
    }
    
    .family-size-badge {
        font-size: 10px;
        padding: 2px 6px;
    }
    
    .household-details {
        font-size: 10px;
    }
    
    .filters-card {
        padding: 0.75rem;
    }
    
    .filters-form {
        gap: 0.5rem;
    }
    
    .filter-group {
        min-width: calc(50% - 0.5rem);
        flex: none;
    }
    
    .filter-label {
        font-size: 0.75rem;
    }
    
    .filter-select {
        font-size: 0.75rem;
        padding: 0.4rem 1.5rem 0.4rem 0.5rem;
    }
    
    .btn-filter-reset {
        padding: 0.4rem 0.75rem;
        font-size: 0.75rem;
    }
}

/* Ultra Small Mobile (360px and below) */
@media (max-width: 360px) {
    .page-container {
        padding: 0 0.125rem;
    }
    
    .dash-header {
        margin-bottom: 0.5rem;
        padding: 0.125rem 0;
    }
    
    .header-text h1 {
        font-size: 1.125rem;
    }
    
    .header-text .sub {
        font-size: 0.75rem;
    }
    
    .alert-success {
        padding: 0.5rem 0.75rem;
        font-size: 0.75rem;
        margin-bottom: 0.75rem;
    }
    
    .table-container {
        max-height: 35vh;
    }
    
    .dist-table {
        min-width: 450px;
        font-size: 10px;
    }
    
    .dist-table th,
    .dist-table td {
        padding: 4px 2px;
    }
    
    .family-size-badge {
        font-size: 9px;
        padding: 1px 4px;
    }
    
    .household-details {
        font-size: 9px;
    }
    
    .filters-card {
        padding: 0.5rem;
    }
    
    .filters-form {
        gap: 0.375rem;
    }
    
    .filter-group {
        min-width: 100%;
    }
    
    .filter-label {
        font-size: 0.7rem;
    }
    
    .filter-select {
        font-size: 0.7rem;
        padding: 0.375rem 1.25rem 0.375rem 0.375rem;
    }
    
    .btn-filter-reset {
        padding: 0.375rem 0.5rem;
        font-size: 0.7rem;
    }
    
    .btn-sm-secondary,
    .btn-sm-primary {
        padding: 3px 6px;
        font-size: 9px;
    }
}
</style>

</div>
@endsection
