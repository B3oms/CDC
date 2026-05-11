@extends('staff.layouts.app')
@section('title', 'Beneficiaries')

@section('content')
<div class="dash-header">
    <h1>Beneficiaries</h1>
    <a href="{{ route('staff.beneficiaries.create') }}" class="btn-primary">+ Add via Interview</a>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

{{-- Slot Summary --}}
<div class="slots-grid">
    @foreach(\App\Models\Barangay::all() as $brgy)
    <div class="slot-card">
        <div class="slot-name">{{ $brgy->name }}</div>
        <div class="slot-bar-wrap">
            @php $used = $slotCounts[$brgy->id] ?? 0; $pct = min(100, ($used/250)*100); @endphp
            <div class="slot-bar">
                <div class="slot-fill {{ $pct >= 100 ? 'full' : ($pct >= 80 ? 'warning' : '') }}"
                    style="width:{{ $pct }}%"></div>
            </div>
        </div>
        <div class="slot-count {{ $used >= 250 ? 'text-danger' : '' }}">
            {{ $used }}/250
        </div>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<div class="filter-row">
    <form method="GET" action="{{ route('staff.beneficiaries.index') }}"
        style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <select name="barangay_id" onchange="this.form.submit()"
            style="padding:6px 12px;border:1px solid #d3d1c7;border-radius:6px;font-size:13px;">
            <option value="">All Barangays</option>
            @foreach($barangays as $b)
            <option value="{{ $b->id }}" {{ request('barangay_id') == $b->id ? 'selected' : '' }}>
                {{ $b->name }}
            </option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()"
            style="padding:6px 12px;border:1px solid #d3d1c7;border-radius:6px;font-size:13px;">
            <option value="">All Status</option>
            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
            <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
        </select>
    </form>
</div>

{{-- Table --}}
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
                    <a href="{{ route('staff.beneficiaries.show', $b->id) }}"
                        class="btn-sm-secondary">View</a>
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