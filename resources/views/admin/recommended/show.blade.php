@extends('admin.layouts.app')
@section('title', 'Recommended Beneficiary Details')
@section('breadcrumb', 'Recommended Beneficiaries > Details')

@section('content')
<div class="dash-header">
    <h1>Recommended Beneficiary Details</h1>
    <x-back-button href="{{ route('recommended.index') }}" />
</div>

<div class="section-card">
    <div class="request-header">
        <h2>Recommendation #{{ str_pad($recommended->id, 4, '0', STR_PAD_LEFT) }}</h2>
        <span class="status-badge status-{{ $recommended->status }}">
            {{ ucfirst($recommended->status) }}
        </span>
    </div>

    <div class="request-grid">
        <div class="request-section">
            <h3>Personal Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Name:</label>
                    <span>{{ $recommended->first_name }}{{ $recommended->middle_name ? ' ' . $recommended->middle_name : '' }} {{ $recommended->last_name }}</span>
                </div>
                <div class="info-item">
                    <label>Gender:</label>
                    <span>{{ $recommended->gender ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <label>Age:</label>
                    <span>{{ $recommended->age ?? 'N/A' }} years old</span>
                </div>
                <div class="info-item">
                    <label>Contact Number:</label>
                    <span>{{ $recommended->contact_number ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <label>Address:</label>
                    <span>{{ $recommended->address ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <label>Barangay:</label>
                    <span>{{ $recommended->barangay->name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <div class="request-section">
            <h3>Recommendation Details</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Priority Level:</label>
                    <span class="priority-badge priority-{{ $recommended->priority_level ?? 'medium' }}">
                        {{ ucfirst($recommended->priority_level ?? 'Medium') }}
                    </span>
                </div>
                <div class="info-item">
                    <label>Reason:</label>
                    <span>{{ $recommended->reason ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <label>Submitted by:</label>
                    <span>{{ $recommended->barangay->name ?? 'Unknown' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="request-meta">
        <p><strong>Submitted:</strong> {{ $recommended->created_at->format('F d, Y - h:i A') }}</p>
        @if($recommended->updated_at->ne($recommended->created_at))
            <p><strong>Last Updated:</strong> {{ $recommended->updated_at->format('F d, Y - h:i A') }}</p>
        @endif
    </div>

    <div class="request-actions">
        @if($recommended->status == 'pending')
            <form action="{{ route('recommended.approve', $recommended->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn-success">Approve</button>
            </form>
            <form action="{{ route('recommended.reject', $recommended->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn-warning">Reject</button>
            </form>
            <form action="{{ route('recommended.convert', $recommended->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn-info">Convert to Beneficiary</button>
            </form>
        @endif
        <form action="{{ route('recommended.destroy', $recommended->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger" onclick="return confirm('Are you sure you want to delete this recommendation?')">Delete</button>
        </form>
    </div>
</div>

<style>
.request-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e9ecef;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-approved {
    background-color: #d4edda;
    color: #155724;
}

.status-rejected {
    background-color: #f8d7da;
    color: #721c24;
}

.status-converted {
    background-color: #cce5ff;
    color: #004085;
}

.priority-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
}

.priority-low {
    background-color: #d4edda;
    color: #155724;
}

.priority-medium {
    background-color: #fff3cd;
    color: #856404;
}

.priority-high {
    background-color: #f8d7da;
    color: #721c24;
}

.priority-critical {
    background-color: #dc3545;
    color: white;
}

.request-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.request-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.request-section h3 {
    margin: 0 0 1rem 0;
    color: #2c3e50;
    font-size: 16px;
}

.info-grid {
    display: grid;
    gap: 1rem;
}

.info-item {
    display: flex;
    gap: 0.5rem;
}

.info-item label {
    font-weight: bold;
    color: #6c757d;
    min-width: 120px;
}

.request-meta {
    margin: 2rem 0;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 4px;
    font-size: 14px;
    color: #6c757d;
}

.request-actions {
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
    display: flex;
    gap: 1rem;
}
</style>
@endsection
