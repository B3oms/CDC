@extends('admin.layouts.app')
@section('title', 'Beneficiaries')

@section('content')
<div class="dash-header">
    <div>
        <h1>Beneficiaries</h1>
        <p class="sub">View beneficiaries registered in your barangay</p>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

{{-- STATISTICS --}}
<div class="dash-grid" style="margin-bottom: 1.5rem;">
    <div class="yearly-col">
        <div class="section-card" style="text-align: center;">
            <h3 style="margin: 0; color: #1a3d1f;">{{ $stats['total'] }}</h3>
            <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 0.875rem;">Total Beneficiaries</p>
        </div>
    </div>
    <div class="yearly-col">
        <div class="section-card" style="text-align: center;">
            <h3 style="margin: 0; color: #059669;">{{ $stats['verified'] }}</h3>
            <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 0.875rem;">Verified</p>
        </div>
    </div>
    <div class="yearly-col">
        <div class="section-card" style="text-align: center;">
            <h3 style="margin: 0; color: #d97706;">{{ $stats['pending'] }}</h3>
            <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 0.875rem;">Pending Verification</p>
        </div>
    </div>
    <div class="yearly-col">
        <div class="section-card" style="text-align: center;">
            <h3 style="margin: 0; color: #dc2626;">{{ $stats['high_vulnerability'] }}</h3>
            <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 0.875rem;">High Vulnerability</p>
        </div>
    </div>
</div>

{{-- FILTERS --}}
<div class="filter-row">
    <form method="GET" action="{{ route('barangay.beneficiaries.index') }}"
        style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">

        {{-- Gender --}}
        <select name="gender"
            onchange="this.form.submit()"
            style="padding:6px 12px;border:1px solid #d3d1c7;border-radius:6px;">
            <option value="">All Gender</option>
            <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
            <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Other</option>
        </select>

        {{-- 4Ps Member --}}
        <select name="is_4ps_member"
            onchange="this.form.submit()"
            style="padding:6px 12px;border:1px solid #d3d1c7;border-radius:6px;">
            <option value="">All 4Ps Status</option>
            <option value="1" {{ request('is_4ps_member') == '1' ? 'selected' : '' }}>4Ps Member</option>
            <option value="0" {{ request('is_4ps_member') == '0' ? 'selected' : '' }}>Non-4Ps Member</option>
        </select>

        {{-- Status --}}
        <select name="status"
            onchange="this.form.submit()"
            style="padding:6px 12px;border:1px solid #d3d1c7;border-radius:6px;">
            <option value="">All Status</option>
            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
        </select>

        {{-- Vulnerability Level --}}
        <select name="vulnerability_level"
            onchange="this.form.submit()"
            style="padding:6px 12px;border:1px solid #d3d1c7;border-radius:6px;">
            <option value="">All Vulnerability Levels</option>
            <option value="High" {{ request('vulnerability_level') == 'High' ? 'selected' : '' }}>High</option>
            <option value="Medium" {{ request('vulnerability_level') == 'Medium' ? 'selected' : '' }}>Medium</option>
            <option value="Low" {{ request('vulnerability_level') == 'Low' ? 'selected' : '' }}>Low</option>
        </select>

    </form>
</div>

{{-- TABLE --}}
<div class="section-card" style="margin-top:1rem;">
    <table class="dist-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Gender</th>
                <th>4Ps Member</th>
                <th>Family Size</th>
                <th>Monthly Income</th>
                <th>Criteria Met</th>
                <th>Vulnerability</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($beneficiaries as $i => $b)
            <tr>
                <td>{{ $beneficiaries->firstItem() + $i }}</td>
                <td>{{ $b->first_name }} {{ $b->last_name }}</td>
                <td>
                    <span style="text-transform: capitalize; font-weight: 500;">
                        {{ $b->gender ?? 'N/A' }}
                    </span>
                </td>
                <td>
                    @if($b->is_4ps_member)
                        <span style="color: #10b981; font-weight: 600;">✓ Yes</span>
                    @else
                        <span style="color: #6b7280;">No</span>
                    @endif
                </td>
                <td>{{ $b->family_size }}</td>
                <td>₱{{ number_format($b->monthly_income, 0) }}</td>

                <td>
                    <span style="font-weight:700;color:{{ $b->criteria_met >= 3 ? '#3b6d11' : '#a32d2d' }}">
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
                        <span class="relief-status-badge ongoing">Verified</span>
                    @else
                        <span class="relief-status-badge upcoming">Pending</span>
                    @endif
                </td>

                <td>
                    <a href="{{ route('barangay.beneficiaries.show', $b->id) }}"
                        class="btn-sm-secondary">
                        View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;color:#888;padding:20px;">
                    No beneficiaries found in your barangay.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:1rem;">
        {{ $beneficiaries->withQueryString()->links() }}
    </div>
</div>

@endsection
