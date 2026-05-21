@extends('admin.layouts.app')

@section('title', 'My Profile')
@section('breadcrumb', 'My Profile')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/barangay-system.css') }}">
<style>
/* Beneficiary Profile Styles */
.profile-page {
    max-width: 100%;
}

.profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
}

/* Profile Cards */
.profile-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.profile-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f3f4f6;
}

.card-icon {
    width: 40px;
    height: 40px;
    background: #1a6b2a;
    color: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
}

/* Personal Info */
.info-group {
    margin-bottom: 1.5rem;
}

.info-group:last-child {
    margin-bottom: 0;
}

.info-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}

.info-value {
    font-size: 0.875rem;
    color: #1f2937;
    font-weight: 500;
}

.info-value.large {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1a6b2a;
}

/* Status Badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-verified {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #10b981;
}

.status-unverified {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fbbf24;
}

/* Family Info */
.family-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

/* Contact Info */
.contact-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.contact-icon {
    width: 32px;
    height: 32px;
    background: #f3f4f6;
    color: #6b7280;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.contact-details {
    flex: 1;
}

.contact-label {
    font-size: 0.75rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.contact-value {
    font-size: 0.875rem;
    color: #1f2937;
    font-weight: 500;
}

/* Summary Stats */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.summary-item {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
}

.summary-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a6b2a;
    margin-bottom: 0.25rem;
}

.summary-label {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
    .profile-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .family-grid {
        grid-template-columns: 1fr;
    }
    
    .summary-grid {
        grid-template-columns: 1fr 1fr;
    }
}
</style>
@endpush

@section('content')
<div class="profile-page">
    <!-- Page Header -->
    <div class="profile-header">
        <h1 class="page-title">My Profile</h1>
    </div>

    <!-- Profile Grid -->
    <div class="profile-grid">
        <!-- Personal Information -->
        <div class="profile-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="card-title">Personal Information</div>
            </div>

            <div class="info-group">
                <div class="info-label">Full Name</div>
                <div class="info-value large">
                    {{ $beneficiary->first_name }} 
                    @if($beneficiary->middle_name) {{ $beneficiary->middle_name . ' ' }} @endif
                    {{ $beneficiary->last_name }}
                    @if($beneficiary->suffix) {{ ', ' . $beneficiary->suffix }} @endif
                </div>
            </div>

            <div class="info-group">
                <div class="info-label">Beneficiary ID</div>
                <div class="info-value">
                    {{ $beneficiary->user->unique_id ?? 'N/A' }}
                </div>
            </div>

            <div class="info-group">
                <div class="info-label">Verification Status</div>
                <div class="status-badge {{ $beneficiary->is_verified ? 'status-verified' : 'status-unverified' }}">
                    <i class="fas fa-{{ $beneficiary->is_verified ? 'check' : 'clock' }}"></i>
                    {{ $beneficiary->is_verified ? 'Verified' : 'Pending Verification' }}
                </div>
            </div>

            <div class="info-group">
                <div class="info-label">Gender</div>
                <div class="info-value">
                    {{ ucfirst($beneficiary->gender) }}
                </div>
            </div>

            <div class="info-group">
                <div class="info-label">Birthdate</div>
                <div class="info-value">
                    @if($beneficiary->birthdate)
                        {{ \Carbon\Carbon::parse($beneficiary->birthdate)->format('F j, Y') }}
                        ({{ \Carbon\Carbon::parse($beneficiary->birthdate)->age }} years old)
                    @else
                        Not specified
                    @endif
                </div>
            </div>

            <div class="info-group">
                <div class="info-label">4Ps Member</div>
                <div class="info-value">
                    {{ $beneficiary->is_4ps_member ? 'Yes' : 'No' }}
                </div>
            </div>
        </div>

        <!-- Family & Contact Information -->
        <div class="profile-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-home"></i>
                </div>
                <div class="card-title">Family & Contact Information</div>
            </div>

            <div class="info-group">
                <div class="info-label">Family Information</div>
                <div class="family-grid">
                    <div>
                        <div class="info-label" style="margin-bottom: 0.25rem;">Family Size</div>
                        <div class="info-value">{{ $beneficiary->family_size ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="info-label" style="margin-bottom: 0.25rem;">Children Count</div>
                        <div class="info-value">{{ $beneficiary->children_count ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="info-label" style="margin-bottom: 0.25rem;">Has Senior</div>
                        <div class="info-value">{{ $beneficiary->has_senior ? 'Yes' : 'No' }}</div>
                    </div>
                    <div>
                        <div class="info-label" style="margin-bottom: 0.25rem;">Monthly Income</div>
                        <div class="info-value">
                            @if($beneficiary->monthly_income)
                                ₱{{ number_format($beneficiary->monthly_income, 2) }}
                            @else
                                Not specified
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-group">
                <div class="info-label">Contact Information</div>
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="contact-details">
                        <div class="contact-label">Contact Number</div>
                        <div class="contact-value">
                            {{ $beneficiary->contact_number ?? 'Not provided' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-group">
                <div class="info-label">Address</div>
                <div class="info-value">
                    {{ $beneficiary->address ?? 'Not provided' }}
                </div>
            </div>

            <div class="info-group">
                <div class="info-label">Barangay</div>
                <div class="info-value">
                    {{ $beneficiary->barangay->name ?? 'Not assigned' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="profile-card">
        <div class="card-header">
            <div class="card-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="card-title">Assistance Summary</div>
        </div>

        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">
                    {{ \App\Models\ReliefEventBeneficiary::where('beneficiary_id', $beneficiary->id)->count() }}
                </div>
                <div class="summary-label">Relief Events</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">
                    {{ \App\Models\Distribution::where('beneficiary_id', $beneficiary->id)->sum('quantity') }}
                </div>
                <div class="summary-label">Items Received</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">
                    {{ $beneficiary->criteria_met ?? 0 }}
                </div>
                <div class="summary-label">Criteria Met</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">
                    {{ $beneficiary->vulnerability_level ?? 'N/A' }}
                </div>
                <div class="summary-label">Vulnerability</div>
            </div>
        </div>
    </div>
</div>
@endsection
