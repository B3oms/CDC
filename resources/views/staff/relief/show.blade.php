@extends('staff.layouts.app')
@section('title', 'Relief Event Details')
@section('breadcrumb', 'Relief Event Details')

@section('content')
<div class="dash-header">
    <div>
        <h1>{{ $event->name }}</h1>
        <p class="sub">
            {{ is_string($event->date) ? date('M d, Y', strtotime($event->date)) : \Carbon\Carbon::parse($event->date)->format('M d, Y') }} ·
            {{ $event->venue }} ·
            <span class="relief-status-badge {{ strtolower($event->status) }}">{{ $event->status }}</span>
        </p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <button onclick="toggleEventStatus({{ $event->id }}, '{{ $event->status }}')" class="btn-status" style="@if($event->status === 'Ongoing') background: #10b981; @else background: #6b7280; @endif">
            @if($event->status === 'Ongoing')
                <i class="fas fa-check-circle"></i> Mark as Finished
            @else
                <i class="fas fa-play-circle"></i> Mark as Ongoing
            @endif
        </button>
        <a href="{{ route('admin.relief.event.pdf', $event->id) }}" class="btn-secondary" target="_blank">
            <i class="fas fa-file-pdf"></i> Download PDF
        </a>
        <a href="{{ route('staff.relief.index') }}" class="btn-back">← Back</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="dash-grid">
    <div class="yearly-col">

        {{-- Event Info --}}
        <div class="section-card">
            <h3>Event Details</h3>
            <table class="dist-table">
                <tr><td class="meta-label">Name</td><td>{{ $event->name }}</td></tr>
                <tr><td class="meta-label">Date</td><td>{{ is_string($event->date) ? date('M d, Y', strtotime($event->date)) : \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</td></tr>
                <tr><td class="meta-label">Venue</td><td>{{ $event->venue }}</td></tr>
                <tr><td class="meta-label">Status</td><td><span class="relief-status-badge {{ strtolower($event->status) }}">{{ $event->status }}</span></td></tr>
                @if($event->calamity)
                <tr><td class="meta-label">Calamity</td><td>{{ $event->calamity->name }}</td></tr>
                @endif
            </table>
        </div>

        {{-- Facilitators --}}
        <div class="section-card">
            <h3>Facilitators</h3>
            @forelse($event->facilitators as $f)
            <div class="partner-item">
                <span class="dot"></span>
                {{ $f->first_name }} {{ $f->last_name }}
                <span class="role-tag">{{ $f->role->name }}</span>
            </div>
            @empty
            <p style="font-size:12px;color:#888;">No facilitators assigned.</p>
            @endforelse
        </div>

        {{-- Barangays --}}
        <div class="section-card">
            <h3>Barangays</h3>
            @foreach($event->eventBarangays as $eb)
            <div class="partner-item">
                <span class="dot"></span>
                {{ $eb->barangay->name }}
                <span class="hint" style="margin:0;">{{ $eb->municipality->name }}</span>
            </div>
            @endforeach
        </div>

    </div>

    <div class="right-col">
        <div class="section-card">
            <h3>Beneficiaries</h3>

            {{-- Barangay Filter --}}
            <div style="margin-bottom:1rem;">
                <form method="GET" action="{{ route('staff.relief.show', $event->id) }}">
                    <select name="barangay_id" onchange="this.form.submit()"
                        style="padding:6px 12px;border:1px solid #d3d1c7;border-radius:6px;font-size:13px;">
                        <option value="">All Barangays</option>
                        @foreach($event->eventBarangays as $eb)
                        <option value="{{ $eb->barangay_id }}"
                            {{ request('barangay_id') == $eb->barangay_id ? 'selected' : '' }}>
                            {{ $eb->barangay->name }}
                        </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <table class="dist-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Barangay</th>
                        <th>Family Size</th>
                        <th>Vulnerability</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($event->beneficiaries as $i => $beneficiary)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</td>
                        <td>{{ $beneficiary->barangay->name ?? 'N/A' }}</td>
                        <td>{{ $beneficiary->family_size }}</td>
                        <td>
                            <span class="badge-intensity {{ strtolower($beneficiary->vulnerability_level ?? 'medium') }}">
                                {{ $beneficiary->vulnerability_level ?? 'Medium' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;color:#888;padding:16px;">
                            No verified beneficiaries found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @if($event->beneficiaries->count())
            <div style="margin-top:1rem;text-align:right;">
                <a href="{{ route('admin.relief.show', $event->id) }}?{{ http_build_query(['barangay_id' => request('barangay_id'), 'pdf' => 1]) }}"
                    class="btn-primary">
                    Export PDF
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.relief-status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.relief-status-badge.ongoing {
    background: #fef3c7;
    color: #d97706;
}

.relief-status-badge.upcoming {
    background: #dbeafe;
    color: #1e40af;
}

.relief-status-badge.done {
    background: #d1fae5;
    color: #059669;
}

.partner-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 0;
    border-bottom: 1px solid #f3f4f6;
}

.partner-item:last-child {
    border-bottom: none;
}

.dot {
    width: 8px;
    height: 8px;
    background: #3b82f6;
    border-radius: 50%;
    flex-shrink: 0;
}

.role-tag {
    background: #e5e7eb;
    color: #374151;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    margin-left: auto;
}

.hint {
    color: #6b7280;
    font-size: 12px;
    margin-left: 8px;
}

.badge-intensity {
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
}

.badge-intensity.low {
    background: #d1fae5;
    color: #059669;
}

.badge-intensity.medium {
    background: #fef3c7;
    color: #d97706;
}

.badge-intensity.high {
    background: #fee2e2;
    color: #dc2626;
}

.dash-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 1.5rem;
}

.yearly-col {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.right-col {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.dist-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.dist-table th {
    background: #f9fafb;
    padding: 10px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
}

.dist-table td {
    padding: 10px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: top;
}

.dist-table tr:last-child td {
    border-bottom: none;
}

.meta-label {
    font-weight: 600;
    color: #374151;
    width: 120px;
}

.btn-status {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    color: white;
}

.btn-status:hover {
    transform: translateY(-1px);
}

.btn-status i {
    font-size: 16px;
}
</style>

@push('scripts')
<script>
function toggleEventStatus(eventId, currentStatus) {
    if (confirm('Are you sure you want to change the event status?')) {
        // Show loading state
        const btn = event.target;
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        btn.disabled = true;
        
        // Determine new status
        const newStatus = currentStatus === 'Ongoing' ? 'Done' : 'Ongoing';
        
        // Send AJAX request to update status
        fetch(`/staff/relief/${eventId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload page to show updated status
                window.location.reload();
            } else {
                alert('Error updating status: ' + data.message);
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating status');
            btn.innerHTML = originalContent;
            btn.disabled = false;
        });
    }
}
</script>
@endpush

