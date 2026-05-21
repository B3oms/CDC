@extends('staff.layouts.app')
@section('title', 'Beneficiaries')

@push('styles')
<style>
/* Unique ID Badge */
.unique-id-badge {
    background: #eaf3de;
    color: #1a6b2a;
    border: 1px solid #1a6b2a;
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    font-family: 'Courier New', monospace;
    letter-spacing: 0.05em;
    display: inline-block;
}
</style>
@endpush

@section('content')
<div class="beneficiaries-page">
    <div class="page-header">
        <h1>Beneficiaries</h1>
        <a href="{{ route('staff.beneficiaries.create') }}" class="btn-add">
            <i class="fas fa-plus"></i> Add via Interview
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- FILTERS --}}
    <div class="filters-card">
        <form method="GET" action="{{ route('staff.beneficiaries.index') }}" id="filterForm" class="filters-form">
            <div class="filter-group">
                <label class="filter-label">Municipality</label>
                <select name="municipality_id" id="municipality" class="filter-select">
                    <option value="">All Municipalities</option>
                    @foreach($municipalities as $m)
                        <option value="{{ $m->id }}" {{ request('municipality_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Barangay</label>
                <select name="barangay_id" id="barangay" class="filter-select" {{ request('municipality_id') ? '' : 'disabled' }}>
                    <option value="">All Barangays</option>
                    @foreach($barangays as $b)
                        <option value="{{ $b->id }}" {{ request('barangay_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Gender</label>
                <select name="gender" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="">All</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">4Ps Status</label>
                <select name="is_4ps_member" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="">All</option>
                    <option value="1" {{ request('is_4ps_member') == '1' ? 'selected' : '' }}>4Ps Member</option>
                    <option value="0" {{ request('is_4ps_member') == '0' ? 'selected' : '' }}>Non-4Ps</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="">All</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <div class="filter-actions">
                <x-pdf-export-dropdown align="left" export-onclick="exportPdf()" />
                <a href="{{ route('staff.beneficiaries.index') }}" class="btn-filter-reset">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Unique ID</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>4Ps</th>
                        <th>Barangay</th>
                        <th>Family</th>
                        <th>Income</th>
                        <th>Criteria</th>
                        <th>Vulnerability</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($beneficiaries as $i => $b)
                    <tr>
                        <td>{{ $beneficiaries->firstItem() + $i }}</td>
                        <td>
                            <span class="unique-id-badge">
                                {{ $b->unique_id ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="td-name">{{ $b->first_name }} {{ $b->last_name }}</td>
                        <td><span class="text-capitalize">{{ $b->gender ?? 'N/A' }}</span></td>
                        <td>
                            @if($b->is_4ps_member)
                                <span class="badge-yes">✓ Yes</span>
                            @else
                                <span class="badge-no">No</span>
                            @endif
                        </td>
                        <td>{{ $b->barangay->name ?? 'N/A' }}</td>
                        <td>{{ $b->family_size }}</td>
                        <td>₱{{ number_format($b->monthly_income, 0) }}</td>
                        <td>
                            <span class="criteria-badge {{ $b->criteria_met >= 3 ? 'criteria-pass' : 'criteria-fail' }}">
                                {{ $b->criteria_met }}/5
                            </span>
                        </td>
                        <td>
                            <span class="badge-intensity {{ strtolower($b->vulnerability_level) }}">
                                {{ $b->vulnerability_level }}
                            </span>
                        </td>
                        <td>
                            @if($b->is_verified)
                                <span class="status-badge verified">Verified</span>
                            @else
                                <span class="status-badge pending">Pending</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('staff.beneficiaries.show', $b->id) }}" class="btn-view">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="td-empty">No beneficiaries found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-pagination">
            {{ $beneficiaries->withQueryString()->links() }}
        </div>
    </div>
</div>

<script>
document.getElementById('municipality').addEventListener('change', function () {
    document.getElementById('barangay').value = '';
    this.form.submit();
});
</script>
@endsection

@push('styles')
<style>
.beneficiaries-page {
    width: 100%;
    max-width: 100%;
}

.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.page-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.btn-add {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1rem;
    background: #1a6b2a;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 0.825rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-add:hover {
    background: #145522;
    transform: translateY(-1px);
}

/* Modern Filters */
.filters-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.filters-form {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: flex-end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    flex: 1;
    min-width: 140px;
}

.filter-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-select {
    padding: 0.5rem 2rem 0.5rem 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.825rem;
    color: #374151;
    background: #f9fafb;
    cursor: pointer;
    transition: all 0.2s ease;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.6rem center;
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
    padding-bottom: 1px;
}

.btn-filter-action {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.5rem 0.9rem;
    background: #dc3545;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn-filter-action:hover {
    background: #b82d3b;
    transform: translateY(-1px);
}

.btn-filter-reset {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.5rem 0.9rem;
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn-filter-reset:hover {
    background: #e5e7eb;
    transform: translateY(-1px);
}

/* Table Card */
.table-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    overflow: hidden;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

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

.td-name {
    font-weight: 500;
    white-space: nowrap;
}

.td-empty {
    text-align: center;
    color: #9ca3af;
    padding: 2rem !important;
    font-style: italic;
}

.text-capitalize {
    text-transform: capitalize;
    font-weight: 500;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.4rem;
    align-items: center;
    flex-wrap: wrap;
}

.btn-view, .btn-edit, .btn-delete {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.4rem 0.7rem;
    border: none;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn-view {
    background: #3b82f6;
    color: white;
}

.btn-view:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.btn-edit {
    background: #f59e0b;
    color: white;
}

.btn-edit:hover {
    background: #d97706;
    transform: translateY(-1px);
}

.btn-delete {
    background: #ef4444;
    color: white;
}

.btn-delete:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

/* Badges */
.badge-yes {
    color: #059669;
    font-weight: 600;
    font-size: 0.8rem;
}

.badge-no {
    color: #9ca3af;
    font-size: 0.8rem;
}

.criteria-badge {
    font-weight: 700;
    font-size: 0.8rem;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
}

.criteria-pass {
    color: #059669;
    background: #ecfdf5;
}

.criteria-fail {
    color: #dc2626;
    background: #fef2f2;
}

.status-badge {
    display: inline-block;
    padding: 0.2rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.status-badge.verified {
    background: #ecfdf5;
    color: #059669;
}

.status-badge.pending {
    background: #fffbeb;
    color: #d97706;
}

.btn-view {
    display: inline-block;
    padding: 0.35rem 0.75rem;
    background: #f3f4f6;
    color: #374151;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-view:hover {
    background: #1a6b2a;
    color: #fff;
}

.table-pagination {
    padding: 0.75rem 1rem;
    border-top: 1px solid #f3f4f6;
}

/* Responsive */
@media (max-width: 1024px) {
    .filter-group {
        min-width: 120px;
    }
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
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
    
    .filter-actions {
        width: 100%;
        justify-content: flex-start;
        margin-top: 0.25rem;
    }

    .modern-table th,
    .modern-table td {
        padding: 0.5rem;
        font-size: 0.75rem;
    }
}

@media (max-width: 480px) {
    .page-header h1 {
        font-size: 1.25rem;
    }

    .filter-group {
        min-width: 100%;
    }
    
    .filter-select {
        font-size: 0.8rem;
    }
}
</style>
@push('scripts')
<script>
function exportPdf() {
    const paperSize = document.getElementById('paperSize').value;
    const orientation = document.getElementById('orientation').value;
    const url = `{{ route('staff.beneficiaries.pdf') }}`;
    
    // Create a hidden form to submit for download
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = url;
    form.style.display = 'none';
    
    const paperSizeInput = document.createElement('input');
    paperSizeInput.type = 'hidden';
    paperSizeInput.name = 'paper_size';
    paperSizeInput.value = paperSize;
    form.appendChild(paperSizeInput);
    
    const orientationInput = document.createElement('input');
    orientationInput.type = 'hidden';
    orientationInput.name = 'orientation';
    orientationInput.value = orientation;
    form.appendChild(orientationInput);
    
    // Add filter parameters if they exist
    const currentUrl = new URL(window.location.href);
    const params = currentUrl.searchParams;
    
    if (params.has('municipality_id')) {
        const municipalityInput = document.createElement('input');
        municipalityInput.type = 'hidden';
        municipalityInput.name = 'municipality_id';
        municipalityInput.value = params.get('municipality_id');
        form.appendChild(municipalityInput);
    }
    
    if (params.has('barangay_id')) {
        const barangayInput = document.createElement('input');
        barangayInput.type = 'hidden';
        barangayInput.name = 'barangay_id';
        barangayInput.value = params.get('barangay_id');
        form.appendChild(barangayInput);
    }
    
    if (params.has('gender')) {
        const genderInput = document.createElement('input');
        genderInput.type = 'hidden';
        genderInput.name = 'gender';
        genderInput.value = params.get('gender');
        form.appendChild(genderInput);
    }
    
    if (params.has('status')) {
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = params.get('status');
        form.appendChild(statusInput);
    }
    
    if (params.has('is_4ps_member')) {
        const fourPsInput = document.createElement('input');
        fourPsInput.type = 'hidden';
        fourPsInput.name = 'is_4ps_member';
        fourPsInput.value = params.get('is_4ps_member');
        form.appendChild(fourPsInput);
    }
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Close dropdown after submission
    closePdfDropdown('pdfOptions');
}
</script>
@endpush