@extends('admin.layouts.app')
@section('title', 'Beneficiary Details')
@section('breadcrumb', 'Beneficiary Details')

@section('content')
<div class="dash-header">
    <div>
        <h1>{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</h1>
        <p class="sub">{{ $beneficiary->barangay->name ?? 'N/A' }}</p>
    </div>
    <a href="{{ route('admin.beneficiaries.index') }}" class="btn-back">← Back</a>
</div>

<div class="dash-grid">
    <div class="yearly-col">

        {{-- Personal Info --}}
        <div class="section-card">
            <h3>Personal Information</h3>
            <table class="dist-table">
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