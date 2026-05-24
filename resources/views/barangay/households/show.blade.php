@extends('admin.layouts.app')

@section('title', 'Household Details')

@section('content')
<div class="household-show-page">
    <div class="page-header">
        <div>
            <a href="{{ route('barangay.households.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Households
            </a>
            <h1>{{ $household->head_of_household }}</h1>
            <p>Household Information and Members</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('barangay.households.edit', $household->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    <div class="household-details">
        <!-- Head of Household Information -->
        <div class="detail-section">
            <h3>Head of Household Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Name</label>
                    <value>{{ $household->head_of_household }}</value>
                </div>
                <div class="info-item">
                    <label>Age</label>
                    <value>{{ $household->age }} years old</value>
                </div>
                <div class="info-item">
                    <label>Sex</label>
                    <value>
                        <span class="sex-badge sex-{{ $household->sex }}">
                            {{ ucfirst($household->sex) }}
                        </span>
                    </value>
                </div>
                <div class="info-item">
                    <label>Birthdate</label>
                    <value>{{ $household->formatted_birthdate }}</value>
                </div>
                <div class="info-item">
                    <label>Contact Number</label>
                    <value>{{ $household->contact_number ?: 'N/A' }}</value>
                </div>
                <div class="info-item">
                    <label>CDC Beneficiary</label>
                    <value>
                        @if($household->is_cdc_beneficiary)
                            <span class="badge badge-success">Yes</span>
                        @else
                            <span class="badge badge-secondary">No</span>
                        @endif
                    </value>
                </div>
                <div class="info-item full-width">
                    <label>Address</label>
                    <value>{{ $household->address }}</value>
                </div>
            </div>
        </div>

        <!-- Family Members -->
        <div class="detail-section">
            <h3>Family Members ({{ $household->total_members }} total)</h3>
            @if($household->members->count() > 0)
                <div class="members-grid">
                    @foreach($household->members as $member)
                        <div class="member-card">
                            <div class="member-info">
                                <h4>{{ $member->name }}</h4>
                                <div class="member-details">
                                    <span class="member-age">{{ $member->age }} years old</span>
                                    <span class="sex-badge sex-{{ $member->sex }}">
                                        {{ ucfirst($member->sex) }}
                                    </span>
                                </div>
                                <div class="relationship">
                                    <strong>Relationship:</strong> {{ $member->relationship_to_head }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-members">
                    <i class="fas fa-users"></i>
                    <p>No additional family members registered</p>
                </div>
            @endif
        </div>

        <!-- Household Statistics -->
        <div class="detail-section">
            <h3>Household Statistics</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ $household->total_members }}</div>
                        <div class="stat-label">Total Members</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ $household->members->where('sex', 'male')->count() + 1 }}</div>
                        <div class="stat-label">Male Members</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ $household->members->where('sex', 'female')->count() }}</div>
                        <div class="stat-label">Female Members</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-child"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ $household->members->where('age', '<', 18)->count() }}</div>
                        <div class="stat-label">Children (< 18)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.household-show-page {
    max-width: 100%;
    padding: 0;
}

.page-header {
    margin-bottom: 2rem;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
    text-decoration: none;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.btn-back:hover {
    color: #374151;
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

.header-actions {
    margin-top: 1rem;
}

.household-details {
    display: grid;
    gap: 2rem;
}

.detail-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.detail-section h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e5e7eb;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-item.full-width {
    grid-column: 1 / -1;
}

.info-item label {
    font-weight: 500;
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.info-item value {
    font-weight: 500;
    color: #1f2937;
    font-size: 1rem;
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

.members-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
}

.member-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
}

.member-info h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.member-details {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.member-age {
    font-size: 0.875rem;
    color: #6b7280;
}

.relationship {
    font-size: 0.875rem;
    color: #374151;
}

.no-members {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}

.no-members i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.stat-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 48px;
    height: 48px;
    background: #3b82f6;
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.stat-info {
    flex: 1;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: none;
    transition: all 0.2s;
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .members-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .detail-section {
        padding: 1rem;
    }
}
</style>
@endpush
