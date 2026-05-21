@extends('admin.layouts.app')
@section('title', 'Beneficiary Details')
@section('breadcrumb', 'Beneficiary Details')

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
        <h1>{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</h1>
        <p class="sub">{{ $beneficiary->barangay->name ?? 'N/A' }}</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.beneficiaries.edit', $beneficiary->id) }}" class="btn-edit">
            <i class="fas fa-edit"></i> Edit
        </a>
        <form action="{{ route('admin.beneficiaries.destroy', $beneficiary->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this beneficiary?')" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-delete">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
        <x-back-button href="{{ route('admin.beneficiaries.index') }}" label="Back" />
    </div>
</div>

<div class="dash-grid">
    <div class="yearly-col">

        {{-- Personal Info --}}
        <div class="section-card">
            <h3>Personal Information</h3>
            <table class="dist-table">
                <tr><td class="meta-label">Unique ID</td>
                    <td><strong>{{ $beneficiary->unique_id ?? 'N/A' }}</strong></td></tr>
                <tr><td class="meta-label">Name</td>
                    <td>{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</td></tr>
                <tr><td class="meta-label">Gender</td>
                    <td>{{ $beneficiary->gender }}</td></tr>
                <tr><td class="meta-label">Birthdate</td>
                    <td>{{ $beneficiary->birthdate
                        ? \Carbon\Carbon::parse($beneficiary->birthdate)->format('M d, Y')
                        : 'N/A' }}</td></tr>
                <tr><td class="meta-label">Contact</td>
                    <td>{{ $beneficiary->contact_number ?? 'N/A' }}</td></tr>
                <tr><td class="meta-label">Address</td>
                    <td>{{ $beneficiary->address ?? 'N/A' }}</td></tr>
                <tr><td class="meta-label">Barangay</td>
                    <td>{{ $beneficiary->barangay->name ?? 'N/A' }}</td></tr>
            </table>
        </div>

        {{-- Verification --}}
        <div class="section-card">
            <h3>Verification Details</h3>
            <table class="dist-table">
                <tr>
                    <td class="meta-label">Status</td>
                    <td>
                        @if($beneficiary->is_verified)
                            <span class="relief-status-badge ongoing">Verified</span>
                        @else
                            <span class="relief-status-badge upcoming">Not Verified</span>
                        @endif
                    </td>
                </tr>
                <tr><td class="meta-label">Criteria Met</td>
                    <td>{{ $beneficiary->criteria_met }}/4</td></tr>
                <tr><td class="meta-label">Vulnerability</td>
                    <td>
                        <span class="badge-intensity {{ strtolower($beneficiary->vulnerability_level) }}">
                            {{ $beneficiary->vulnerability_level }}
                        </span>
                    </td>
                </tr>
                <tr><td class="meta-label">Monthly Income</td>
                    <td>₱{{ number_format($beneficiary->monthly_income, 0) }}</td></tr>
                <tr><td class="meta-label">Family Size</td>
                    <td>{{ $beneficiary->family_size }} members</td></tr>
                <tr><td class="meta-label">Children (≤12)</td>
                    <td>{{ $beneficiary->children_count }}</td></tr>
                <tr><td class="meta-label">Has Senior</td>
                    <td>{{ $beneficiary->has_senior ? 'Yes' : 'No' }}</td></tr>
                <tr><td class="meta-label">Interviewed by</td>
                    <td>{{ $beneficiary->interviewer?->first_name }}
                        {{ $beneficiary->interviewer?->last_name }}</td></tr>
                <tr><td class="meta-label">Interview Date</td>
                    <td>{{ $beneficiary->interviewed_at?->format('M d, Y h:i A') }}</td></tr>
            </table>
            @if($beneficiary->interview_notes)
            <div style="margin-top:10px;padding:10px;background:#f1efe8;border-radius:6px;font-size:13px;">
                <strong>Notes:</strong> {{ $beneficiary->interview_notes }}
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
                        <td>
                            <span class="badge-{{ strtolower($d->status) }}">
                                {{ $d->status }}
                            </span>
                        </td>
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