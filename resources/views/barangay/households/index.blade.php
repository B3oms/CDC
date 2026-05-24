@extends('admin.layouts.app')

@section('title', 'Households')

@section('content')
<div class="households-page">
    <div class="page-header">
        <div>
            <h1>Households</h1>
            <p>Manage households in your barangay</p>
        </div>
        <a href="{{ route('barangay.households.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Household
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <div class="households-table-container">
        @if($households->count() > 0)
            <div class="table-responsive">
                <table class="households-table">
                    <thead>
                        <tr>
                            <th>Head of Household</th>
                            <th>Age</th>
                            <th>Sex</th>
                            <th>Contact</th>
                            <th>CDC Beneficiary</th>
                            <th>Total Members</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($households as $household)
                            <tr>
                                <td>
                                    <strong>{{ $household->head_of_household }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $household->formatted_birthdate }}</small>
                                </td>
                                <td>{{ $household->age }}</td>
                                <td>
                                    <span class="sex-badge sex-{{ $household->sex }}">
                                        {{ ucfirst($household->sex) }}
                                    </span>
                                </td>
                                <td>{{ $household->contact_number ?: 'N/A' }}</td>
                                <td>
                                    @if($household->is_cdc_beneficiary)
                                        <span class="badge badge-success">Yes</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                                <td>{{ $household->total_members }}</td>
                                <td>
                                    <div class="address-cell">
                                        {{ Str::limit($household->address, 50) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('barangay.households.show', $household) }}" 
                                           class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('barangay.households.edit', $household) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('barangay.households.destroy', $household) }}" 
                                              style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this household?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                {{ $households->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-home"></i>
                </div>
                <h3>No Households Found</h3>
                <p>Start by adding your first household to the system.</p>
                <a href="{{ route('barangay.households.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Household
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.households-page {
    max-width: 100%;
    padding: 0;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.page-header p {
    color: #6b7280;
    font-size: 1rem;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-weight: 500;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.households-table-container {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.table-responsive {
    overflow-x: auto;
}

.households-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.households-table th {
    background: #f9fafb;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
}

.households-table td {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    vertical-align: top;
}

.households-table tr:hover {
    background: #f9fafb;
}

.sex-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: capitalize;
}

.sex-male {
    background: #dbeafe;
    color: #1e40af;
}

.sex-female {
    background: #fce7f3;
    color: #a21caf;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.badge-secondary {
    background: #f3f4f6;
    color: #374151;
}

.address-cell {
    max-width: 200px;
    word-wrap: break-word;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    transition: all 0.2s;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-info {
    background: #06b6d4;
    color: white;
}

.btn-info:hover {
    background: #0891b2;
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}

.pagination-container {
    margin-top: 1.5rem;
    display: flex;
    justify-content: center;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
}

.empty-state-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.empty-state p {
    font-size: 1rem;
    margin-bottom: 2rem;
}

.text-muted {
    color: #6b7280;
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .households-table-container {
        padding: 1rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>
@endpush
