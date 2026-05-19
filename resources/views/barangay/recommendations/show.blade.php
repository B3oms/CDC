@extends('admin.layouts.app')
@section('title', 'Recommendation Details')
@section('breadcrumb', 'Recommendations > Details')

@section('content')
<div class="dash-header">
    <h1>Recommendation Details</h1>
    <div>
        <a href="{{ route('barangay.recommendations.index') }}" class="btn-secondary">← Back</a>
        @if($recommendation->status == 'pending')
            <a href="{{ route('barangay.recommendations.edit', $recommendation->id) }}" class="btn-primary">Edit</a>
        @endif
    </div>
</div>

<div class="section-card">
    <div class="request-header">
        <h2>Recommendation #{{ str_pad($recommendation->id, 4, '0', STR_PAD_LEFT) }}</h2>
        <span class="status-badge status-{{ $recommendation->status }}">
            {{ ucfirst($recommendation->status) }}
        </span>
    </div>

    <div class="request-grid">
        <div class="request-section">
            <h3>Personal Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Name:</label>
                    <span>{{ $recommendation->full_name }}</span>
                </div>
                <div class="info-item">
                    <label>Contact Number:</label>
                    <span>{{ $recommendation->contact_number ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <label>Address:</label>
                    <span>{{ $recommendation->address }}</span>
                </div>
                <div class="info-item">
                    <label>Age:</label>
                    <span>{{ $recommendation->age }} years old</span>
                </div>
                <div class="info-item">
                    <label>Gender:</label>
                    <span>{{ ucfirst($recommendation->gender) }}</span>
                </div>
                <div class="info-item">
                    <label>Family Size:</label>
                    <span>{{ $recommendation->family_size }} members</span>
                </div>
            </div>
        </div>

        <div class="request-section">
            <h3>Economic Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Monthly Income:</label>
                    <span>₱{{ number_format($recommendation->monthly_income, 2) }}</span>
                </div>
                <div class="info-item">
                    <label>Income Level:</label>
                    <span>{{ $recommendation->income_level }}</span>
                </div>
                <div class="info-item">
                    <label>4Ps Member:</label>
                    <span>{{ $recommendation->is_4ps_member ? 'Yes' : 'No' }}</span>
                </div>
                <div class="info-item">
                    <label>Has Senior Citizen:</label>
                    <span>{{ $recommendation->has_senior ? 'Yes' : 'No' }}</span>
                </div>
                <div class="info-item">
                    <label>Children Count:</label>
                    <span>{{ $recommendation->children_count }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="request-section">
        <h3>Recommendation Details</h3>
        <div class="info-grid">
            <div class="info-item">
                <label>Priority Level:</label>
                <span class="priority-badge priority-{{ $recommendation->priority_level }}">
                    {{ ucfirst($recommendation->priority_level) }}
                </span>
            </div>
            <div class="info-item">
                <label>Reason for Recommendation:</label>
                <span>{{ $recommendation->reason }}</span>
            </div>
            @if($recommendation->special_circumstances)
            <div class="info-item">
                <label>Special Circumstances:</label>
                <span>{{ $recommendation->special_circumstances }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="request-meta">
        <p><strong>Submitted:</strong> {{ $recommendation->created_at->format('F d, Y - h:i A') }}</p>
        @if($recommendation->updated_at->ne($recommendation->created_at))
            <p><strong>Last Updated:</strong> {{ $recommendation->updated_at->format('F d, Y - h:i A') }}</p>
        @endif
        <p><strong>Recommended by:</strong> {{ $recommendation->barangay->name ?? 'Unknown' }}</p>
    </div>

    @if($recommendation->status == 'pending')
        <div class="request-actions">
            <a href="{{ route('barangay.recommendations.edit', $recommendation->id) }}" class="btn-primary">Edit Recommendation</a>
        </div>
    @endif
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
    min-width: 140px;
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
}
</style>
@endsection
