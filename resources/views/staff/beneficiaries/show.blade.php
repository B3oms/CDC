@extends('staff.layouts.app')
@section('title', 'Beneficiary Details')

@push('styles')
<style>
.header-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex-wrap: wrap;
}

.btn-edit, .btn-delete {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1rem;
    border: none;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
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

@media (max-width: 768px) {
    .header-actions {
        width: 100%;
        justify-content: flex-start;
        margin-top: 1rem;
    }
    
    .dash-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
@endpush

@section('content')
<div class="dash-header">
    <div>
        <h1>{{ $beneficiary->first_name }} {{ $beneficiary->middle_name }} {{ $beneficiary->last_name }}</h1>
        <p class="sub">{{ $beneficiary->barangay->name ?? 'N/A' }}</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('staff.beneficiaries.edit', $beneficiary->id) }}" class="btn-edit">
            <i class="fas fa-edit"></i> Edit
        </a>
        <form action="{{ route('staff.beneficiaries.destroy', $beneficiary->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this beneficiary?')" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-delete">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
        <x-back-button href="{{ route('staff.beneficiaries.index') }}" label="Back" />
    </div>
</div>

<div class="dash-grid">
    <div class="yearly-col">

        {{-- Personal Info --}}
        <div class="section-card">
            <h3>Personal Information</h3>
            <table class="dist-table">
                <tr><td class="meta-label">Unique ID</td><td><strong>{{ $beneficiary->unique_id ?? 'N/A' }}</strong></td></tr>
                <tr><td class="meta-label">Full Name</td><td>{{ $beneficiary->first_name }} {{ $beneficiary->middle_name }} {{ $beneficiary->last_name }} {{ $beneficiary->suffix ?? '' }}</td></tr>
                <tr><td class="meta-label">Gender</td><td>{{ $beneficiary->gender }}</td></tr>
                <tr><td class="meta-label">Age</td><td>{{ $beneficiary->age ?? 'N/A' }} years old</td></tr>
                <tr><td class="meta-label">Birthdate</td><td>{{ $beneficiary->birthdate ? \Carbon\Carbon::parse($beneficiary->birthdate)->format('M d, Y') : 'N/A' }}</td></tr>
                <tr><td class="meta-label">Contact</td><td>{{ $beneficiary->contact_number ?? 'N/A' }}</td></tr>
                <tr><td class="meta-label">Address</td><td>{{ $beneficiary->address ?? 'N/A' }}</td></tr>
                <tr><td class="meta-label">Barangay</td><td>{{ $beneficiary->barangay->name ?? 'N/A' }}</td></tr>
            </table>
        </div>

        {{-- Family Background --}}
        <div class="section-card">
            <h3>Family Background</h3>
            

            {{-- Spouse Information --}}
            @if($beneficiary->spouse_name || $beneficiary->spouse_age || $beneficiary->spouse_sex || $beneficiary->spouse_birthdate || $beneficiary->spouse_occupation)
            <div style="margin-bottom: 1.5rem;">
                <h4 style="color: #3b82f6; margin-bottom: 0.5rem;">Spouse Information</h4>
                <table class="dist-table">
                    @if($beneficiary->spouse_name)
                    <tr><td class="meta-label">Name</td><td>{{ $beneficiary->spouse_name }}</td></tr>
                    @endif
                    @if($beneficiary->spouse_age)
                    <tr><td class="meta-label">Age</td><td>{{ $beneficiary->spouse_age }} years old</td></tr>
                    @endif
                    @if($beneficiary->spouse_sex)
                    <tr><td class="meta-label">Sex</td><td>{{ ucfirst($beneficiary->spouse_sex) }}</td></tr>
                    @endif
                    @if($beneficiary->spouse_birthdate)
                    <tr><td class="meta-label">Birthdate</td><td>{{ \Carbon\Carbon::parse($beneficiary->spouse_birthdate)->format('M d, Y') }}</td></tr>
                    @endif
                    @if($beneficiary->spouse_occupation)
                    <tr><td class="meta-label">Occupation</td><td>{{ $beneficiary->spouse_occupation }}</td></tr>
                    @endif
                </table>
            </div>
            @endif

            {{-- Children Information --}}
            @if($beneficiary->children && is_array($beneficiary->children) && count($beneficiary->children) > 0)
            <div style="margin-bottom: 1.5rem;">
                <h4 style="color: #3b82f6; margin-bottom: 0.5rem;">Children Information</h4>
                @foreach($beneficiary->children as $index => $child)
                <div style="margin-bottom: 1rem; padding: 0.75rem; background: #f8f9fa; border-radius: 6px;">
                    <h5 style="color: #6b7280; margin-bottom: 0.5rem;">Child {{ $index + 1 }}</h5>
                    <table class="dist-table" style="font-size: 0.9rem;">
                        @if($child['name'])
                        <tr><td class="meta-label">Name</td><td>{{ $child['name'] }}</td></tr>
                        @endif
                        @if($child['age'])
                        <tr><td class="meta-label">Age</td><td>{{ $child['age'] }} years old</td></tr>
                        @endif
                        @if($child['sex'])
                        <tr><td class="meta-label">Sex</td><td>{{ ucfirst($child['sex']) }}</td></tr>
                        @endif
                        @if($child['birthdate'])
                        <tr><td class="meta-label">Birthdate</td><td>{{ \Carbon\Carbon::parse($child['birthdate'])->format('M d, Y') }}</td></tr>
                        @endif
                    </table>
                </div>
                @endforeach
            </div>
            @endif

            {{-- No Family Data --}}
            @if(!$beneficiary->spouse_name && (!$beneficiary->children || empty($beneficiary->children)))
            <div style="text-align: center; color: #6b7280; padding: 2rem;">
                <i class="fas fa-info-circle" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                <p>No family background information available</p>
            </div>
            @endif
        </div>

        {{-- Verification --}}
        <div class="section-card">
            <h3>Verification Details</h3>
            <table class="dist-table">
                <tr>
                    <td class="meta-label">Status</td>
                    <td>
                        @if($beneficiary->status === 'verified')
                            <span class="relief-status-badge ongoing">Verified</span>
                        @elseif($beneficiary->status === 'rejected')
                            <span class="relief-status-badge expired">Rejected</span>
                        @else
                            <span class="relief-status-badge upcoming">Pending</span>
                        @endif
                    </td>
                </tr>
                <tr><td class="meta-label">Criteria Met</td><td>{{ $beneficiary->criteria_met }}/5</td></tr>
                <tr><td class="meta-label">Vulnerability</td>
                    <td><span class="badge-intensity {{ strtolower($beneficiary->vulnerability_level) }}">{{ $beneficiary->vulnerability_level }}</span></td>
                </tr>
                <tr><td class="meta-label">Monthly Income</td><td>₱{{ number_format($beneficiary->monthly_income, 0) }}</td></tr>
                <tr><td class="meta-label">Family Size</td><td>{{ $beneficiary->family_size }} members</td></tr>
                <tr><td class="meta-label">Children (≤12)</td><td>{{ $beneficiary->children_count }}</td></tr>
                <tr><td class="meta-label">Has Senior</td><td>{{ $beneficiary->has_senior ? 'Yes' : 'No' }}</td></tr>
                <tr><td class="meta-label">Interviewed by</td><td>{{ $beneficiary->interviewer?->first_name }} {{ $beneficiary->interviewer?->last_name }}</td></tr>
                <tr><td class="meta-label">Interview Date</td><td>{{ $beneficiary->interviewed_at?->format('M d, Y h:i A') }}</td></tr>
                @if($beneficiary->status === 'rejected')
                <tr><td class="meta-label">Rejection Date</td><td>{{ $beneficiary->rejection_date ? \Carbon\Carbon::parse($beneficiary->rejection_date)->format('M d, Y') : 'N/A' }}</td></tr>
                <tr><td class="meta-label">Scheduled Deletion</td><td>{{ $beneficiary->scheduled_deletion_date ? \Carbon\Carbon::parse($beneficiary->scheduled_deletion_date)->format('M d, Y') : 'N/A' }}</td></tr>
                <tr><td class="meta-label">Days Until Deletion</td><td>
                    @if($beneficiary->scheduled_deletion_date)
                        {{ \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($beneficiary->scheduled_deletion_date), false) }} days
                    @else
                        N/A
                    @endif
                </td></tr>
                @endif
            </table>
            @if($beneficiary->interview_notes)
            <div style="margin-top:10px;padding:10px;background:#f1efe8;border-radius:6px;font-size:13px;">
                <strong>Notes:</strong> {{ $beneficiary->interview_notes }}
            </div>
            @endif
            @if($beneficiary->status === 'rejected')
            <div style="margin-top:10px;padding:10px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;font-size:13px;">
                <strong style="color: #dc2626;">⚠️ Rejection Notice:</strong> This beneficiary did not meet the verification criteria and will be automatically deleted from the system after 10 days from the rejection date.
            </div>
            @endif
        </div>

    </div>

    <div class="right-col">
        {{-- Relief Distribution History --}}
        <div class="section-card">
            <h3>Relief Distribution History</h3>
            <table class="dist-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Operation</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($beneficiary->distributions as $d)
                    <tr>
                        <td>{{ $d->date_distributed }}</td>
                        <td>{{ $d->reliefOperation->calamity->name ?? 'N/A' }}</td>
                        <td><span class="badge-{{ strtolower($d->status) }}">{{ $d->status }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align:center;color:#888;padding:16px;">
                            No distributions yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection