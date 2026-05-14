@extends('admin.layouts.app')
@section('title', 'Beneficiaries')
@section('breadcrumb', 'Beneficiaries')

@section('content')
<div class="dash-header">
    <h1>Beneficiaries</h1>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

{{-- FILTERS --}}
<div class="filter-row">
    <form method="GET" action="{{ route('admin.beneficiaries.index') }}"
        id="filterForm"
        style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">

        {{-- Municipality --}}
        <select name="municipality_id" id="municipality"
            onchange="document.getElementById('filterForm').submit()"
            style="padding:6px 12px;border:1px solid #d3d1c7;border-radius:6px;">
            <option value="">Select Municipality</option>
            @foreach($municipalities as $m)
                <option value="{{ $m->id }}"
                    {{ request('municipality_id') == $m->id ? 'selected' : '' }}>
                    {{ $m->name }}
                </option>
            @endforeach
        </select>

        {{-- Barangay --}}
        <select name="barangay_id" id="barangay"
            onchange="document.getElementById('filterForm').submit()"
            style="padding:6px 12px;border:1px solid #d3d1c7;border-radius:6px;"
            {{ request('municipality_id') ? '' : 'disabled' }}>
            <option value="">Select Barangay</option>
            @foreach($barangays as $b)
                <option value="{{ $b->id }}"
                    {{ request('barangay_id') == $b->id ? 'selected' : '' }}>
                    {{ $b->name }}
                </option>
            @endforeach
        </select>

        {{-- Status --}}
        <select name="status"
            onchange="document.getElementById('filterForm').submit()"
            style="padding:6px 12px;border:1px solid #d3d1c7;border-radius:6px;">
            <option value="">All Status</option>
            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
        </select>

        {{-- Download PDF --}}
        <a href="{{ route('admin.beneficiaries.pdf', request()->query()) }}"
            class="btn-sm-secondary"
            style="text-decoration:none;">
            Download PDF
        </a>

    </form>
</div>

{{-- TABLE --}}
<div class="section-card" style="margin-top:1rem;">
    <table class="dist-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Barangay</th>
                <th>Family Size</th>
                <th>Income</th>
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
                <td>{{ $b->barangay->name ?? 'N/A' }}</td>
                <td>{{ $b->family_size }}</td>
                <td>₱{{ number_format($b->monthly_income, 0) }}</td>

                <td>
                    <span style="font-weight:700;color:{{ $b->criteria_met >= 2 ? '#3b6d11' : '#a32d2d' }}">
                        {{ $b->criteria_met }}/4
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
                    <a href="{{ route('admin.beneficiaries.show', $b->id) }}"
                        class="btn-sm-secondary">
                        View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center;color:#888;padding:20px;">
                    No beneficiaries found.
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