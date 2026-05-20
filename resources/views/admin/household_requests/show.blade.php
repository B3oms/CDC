@extends('admin.layouts.app')
@section('title', 'Household Request Review')
@section('breadcrumb', 'Household Request Review')

@section('content')
<div class="dash-header">
    <div>
        <h1>Household Request #{{ $request->id }}</h1>
        <p class="sub">Review and approve/reject household assistance request</p>
    </div>
    <div>
        <a href="{{ route('admin.household_requests.index') }}" class="btn-back">← Back</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="dash-grid">
    <div class="yearly-col">
        <div class="section-card" style="position: relative; padding-top: 40px;">
            <h3>Head of Household</h3>
            <span class="status-badge {{ $request->status }}" style="position: absolute; top: 15px; right: 15px; font-size: 12px; padding: 6px 12px;">
                {{ ucfirst($request->status) }}
            </span>
            <table class="dist-table">
                <tr><td class="meta-label">Name</td><td>{{ $request->head_of_household }}</td></tr>
                <tr><td class="meta-label">Age</td><td>{{ $request->head_age ?? 'Not specified' }} years old</td></tr>
                <tr><td class="meta-label">Sex</td><td>{{ $request->head_sex ? ucfirst($request->head_sex) : 'Not specified' }}</td></tr>
                <tr><td class="meta-label">Date of Birth</td><td>{{ $request->birthday ? $request->birthday->format('M d, Y') : 'Not specified' }}</td></tr>
                <tr><td class="meta-label">Contact Number</td><td>{{ $request->formatted_contact_number ?: 'Not provided' }}</td></tr>
                <tr><td class="meta-label">Address</td><td>{{ $request->address }}</td></tr>
                <tr><td class="meta-label">Barangay</td><td>{{ $request->barangay->name }}</td></tr>
                <tr><td class="meta-label">Submitted</td><td>{{ $request->created_at->format('M d, Y h:i A') }}</td></tr>
            </table>
        </div>

        @if($request->isRejected())
            <div class="section-card" style="margin-top: 20px;">
                <h3>Reason for Rejection</h3>
                <p>{{ $request->rejection_reason }}</p>
            </div>
        @endif
        
        @if($request->isApproved() && $request->approvedBy)
            <div class="section-card" style="margin-top: 20px;">
                <h3>Approved by</h3>
                <p>{{ $request->approvedBy->first_name }} {{ $request->approvedBy->last_name }}</p>
                <p>{{ $request->approved_at->format('M d, Y h:i A') }}</p>
            </div>
        @endif
        
        @if($request->notes)
            <div class="section-card" style="margin-top: 20px;">
                <h3>Staff Notes</h3>
                <p>{{ $request->notes }}</p>
            </div>
        @endif
    </div>

    <div class="yearly-col">
        <div class="section-card">
            <h3>Family Members</h3>
            @if($request->members->isEmpty())
                <p>No additional family members listed.</p>
            @else
                <div class="family-members-list">
                    @foreach($request->members as $member)
                    <div class="family-member-item">
                        <div class="member-name">{{ $member->name }}</div>
                        <div class="member-details">
                            <span class="member-age">{{ $member->age }} years old</span>
                            <span class="member-sex">{{ ucfirst($member->sex) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
            
            <div class="household-size-summary">
                <strong>Total Household Size:</strong> {{ $request->family_size }} members
            </div>
        </div>
    </div>
</div>

@if($request->isPending())
<div class="section-card" style="margin-top: 20px;">
    <h3>Review Actions</h3>
    
    <div class="dash-grid">
        <div class="yearly-col">
            <!-- Approve Form -->
            <form method="POST" action="{{ route('admin.household_requests.approve', $request->id) }}">
                @csrf
                <div class="form-group">
                    <label>Staff Notes (Optional)</label>
                    <textarea name="staff_notes" rows="3" placeholder="Add any notes about this approval..."></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%;">
                    <i class="fas fa-check"></i> Approve Request
                </button>
            </form>
        </div>
        
        <div class="yearly-col">
            <!-- Reject Form -->
            <form method="POST" action="{{ route('admin.household_requests.reject', $request->id) }}">
                @csrf
                <div class="form-group">
                    <label>Rejection Reason *</label>
                    <textarea name="rejection_reason" rows="3" required placeholder="Please provide a reason for rejection..."></textarea>
                </div>
                <div class="form-group">
                    <label>Staff Notes (Optional)</label>
                    <textarea name="staff_notes" rows="2" placeholder="Additional notes..."></textarea>
                </div>
                <button type="submit" class="btn-danger" style="width: 100%;">
                    <i class="fas fa-times"></i> Reject Request
                </button>
            </form>
        </div>
    </div>
</div>
@endif

<style>
.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.pending {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.approved {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.rejected {
    background: #fee2e2;
    color: #991b1b;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.form-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-family: inherit;
    resize: vertical;
}

.family-members-list {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #ffffff;
}

.family-member-item {
    padding: 12px 15px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
    color: #374151;
    flex: 1;
}

.family-member-item:last-child {
    border-bottom: none;
}

.member-name {
    font-weight: 600;
    color: #374151;
    flex: 1;
}

.member-details {
    display: flex;
    gap: 15px;
    align-items: center;
}

.member-age, .member-sex {
    font-size: 14px;
    color: #6b7280;
    background: #f3f4f6;
    padding: 4px 8px;
    border-radius: 12px;
    font-weight: 500;
}

.household-size-summary {
    margin-top: 15px;
    padding: 12px 15px;
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 8px;
    color: #0369a1;
    font-weight: 500;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .family-member-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .member-details {
        align-self: flex-end;
    }
}
</style>
@endsection
