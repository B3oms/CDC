@extends('admin.layouts.app')
@section('title', 'Households')

@section('content')
<div class="dash-header">
    <div>
        <h1>Households</h1>
        <p class="sub">View all registered households in your barangay</p>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

{{-- FILTERS --}}
<div class="filter-row">
    <form method="GET" action="{{ route('barangay.household_requests.households') }}"
        style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">

        {{-- Family Size --}}
        <select name="family_size"
            onchange="this.form.submit()"
            style="padding:6px 12px;border:1px solid #d3d1c7;border-radius:6px;">
            <option value="">All Family Sizes</option>
            @for($i = 1; $i <= 10; $i++)
                <option value="{{ $i }}" {{ request('family_size') == $i ? 'selected' : '' }}>
                    {{ $i }} members
                </option>
            @endfor
        </select>

    </form>
</div>

{{-- HOUSEHOLDS TABLE --}}
<div class="section-card" style="margin-top:1rem;">
    <table class="dist-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Head of Household</th>
                <th>Contact</th>
                <th>Family Size</th>
                <th>Address</th>
                <th>Approved</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($households as $i => $household)
            <tr>
                <td>{{ $households->firstItem() + $i }}</td>
                <td>
                    <div>
                        <strong>{{ $household->head_of_household }}</strong>
                        @if($household->head_age)
                            <br><small style="color: #6b7280;">{{ $household->head_age }} years, {{ ucfirst($household->head_sex) }}</small>
                        @endif
                    </div>
                </td>
                <td>{{ $household->formatted_contact_number ?? 'N/A' }}</td>
                <td>
                    <span style="background: #e5e7eb; color: #374151; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                        {{ $household->family_size }} members
                    </span>
                </td>
                <td>{{ $household->address }}</td>
                <td>{{ $household->approved_at ? $household->approved_at->format('M d, Y') : 'N/A' }}</td>
                <td>
                    <a href="{{ route('barangay.household_requests.show', $household->id) }}"
                        class="btn-sm-secondary" 
                        style="background: linear-gradient(135deg, #6b7280, #4b5563);
                               border: 1px solid #6b7280;
                               color: white;
                               padding: 6px 12px;
                               border-radius: 6px;
                               font-size: 12px;
                               font-weight: 500;
                               text-decoration: none;
                               display: inline-block;
                               transition: all 0.2s ease;
                               box-shadow: 0 2px 4px rgba(107, 114, 128, 0.1);">
                        <i class="fas fa-eye" style="margin-right: 4px;"></i>View
                    </a>
                    <a href="{{ route('barangay.household_requests.edit', $household->id) }}"
                        class="btn-sm-primary" 
                        style="margin-left: 5px; 
                               background: linear-gradient(135deg, #1a3d1f, #2d5a31);
                               border: 1px solid #1a3d1f;
                               color: white;
                               padding: 6px 12px;
                               border-radius: 6px;
                               font-size: 12px;
                               font-weight: 500;
                               text-decoration: none;
                               display: inline-block;
                               transition: all 0.2s ease;
                               box-shadow: 0 2px 4px rgba(26, 61, 31, 0.1);">
                        <i class="fas fa-edit" style="margin-right: 4px;"></i>Edit
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;color:#888;padding:20px;">
                    No households found in your barangay.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:1rem;">
        {{ $households->withQueryString()->links() }}
    </div>
</div>

<style>
.btn-sm-secondary:hover {
    background: linear-gradient(135deg, #4b5563, #6b7280) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(107, 114, 128, 0.2) !important;
}

.btn-sm-secondary:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(107, 114, 128, 0.1) !important;
}

.btn-sm-primary:hover {
    background: linear-gradient(135deg, #2d5a31, #1a3d1f) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(26, 61, 31, 0.2) !important;
}

.btn-sm-primary:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(26, 61, 31, 0.1) !important;
}
</style>

@endsection
