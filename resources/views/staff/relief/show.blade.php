@extends('staff.layouts.app')
@section('title', 'Relief Event Details')
@section('breadcrumb', 'Relief Event Details')

@section('content')
<div class="dash-header">
    <div style="text-align: left;">
        <h1 style="text-align: left;">{{ $event->name }}</h1>
        <p class="sub">
            {{ is_string($event->date) ? date('M d, Y', strtotime($event->date)) : \Carbon\Carbon::parse($event->date)->format('M d, Y') }} ·
            {{ $event->venue }} ·
            <span class="relief-status-badge {{ strtolower($event->status) }}">{{ $event->status }}</span>
        </p>
    </div>
    <div class="dash-header-actions">
        <button onclick="toggleEventStatus({{ $event->id }}, '{{ $event->status }}')" class="btn-status {{ $event->status === 'Ongoing' ? 'btn-ongoing' : 'btn-default' }}">
            @if($event->status === 'Ongoing')
                <i class="fas fa-check-circle"></i> Mark as Finished
            @else
                <i class="fas fa-play-circle"></i> Mark as Ongoing
            @endif
        </button>
        <x-pdf-export-dropdown export-onclick="exportPdf({{ $event->id }})" />
        <x-back-button href="{{ route('staff.relief.index') }}" label="Back" />
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="relief-event-container">

    {{-- Left Column: Event Details, Facilitators, Barangays --}}
    <div class="left-column">

        {{-- Event Info --}}
        <div class="event-card">
            <h3>Event Details</h3>
            <table class="info-table">
                <tr><td class="label">Name</td><td>{{ $event->name }}</td></tr>
                <tr><td class="label">Date</td><td>{{ is_string($event->date) ? date('M d, Y', strtotime($event->date)) : \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</td></tr>
                <tr><td class="label">Venue</td><td>{{ $event->venue }}</td></tr>
                <tr><td class="label">Status</td><td><span class="relief-status-badge {{ strtolower($event->status) }}">{{ $event->status }}</span></td></tr>
                @if($event->calamity_type)
                <tr><td class="label">Calamity Type</td><td>{{ $event->calamity_type }}</td></tr>
                @endif
            </table>
        </div>

        {{-- Facilitators --}}
        <div class="event-card">
            <h3>Facilitators</h3>
            @forelse($event->facilitators as $f)
            <div class="facilitator-item">
                <span class="facilitator-name">{{ $f->first_name }} {{ $f->last_name }}</span>
                <span class="role-tag">{{ $f->role->name }}</span>
            </div>
            @empty
            <p class="empty-message">No facilitators assigned.</p>
            @endforelse
        </div>

        {{-- Barangays --}}
        <div class="event-card">
            <h3>Barangays</h3>
            @forelse($event->eventBarangays as $eb)
            <div class="barangay-item">
                <div class="barangay-name">{{ $eb->barangay->name }}</div>
                <div class="municipality-name">{{ $eb->municipality->name }}</div>
            </div>
            @empty
            <p class="empty-message">No barangays assigned.</p>
            @endforelse
        </div>

    </div>

    {{-- Right Column: Distributed Items, Beneficiaries --}}
    <div class="right-column">

        {{-- Distributed Items --}}
        @if($event->distributedItems->isNotEmpty())
        <div class="event-card">
            <h3>Distributed Items</h3>
            <div class="scroll-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Total Qty</th>
                            <th>Per Beneficiary</th>
                            <th>Beneficiaries</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($event->distributedItems as $distributedItem)
                        <tr>
                            <td>{{ $distributedItem->item->name }}</td>
                            <td>{{ $distributedItem->total_quantity }} {{ $distributedItem->unit }}</td>
                            <td>{{ $distributedItem->per_beneficiary }} {{ $distributedItem->unit }}</td>
                            <td>{{ $distributedItem->beneficiaries_count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Beneficiaries --}}
        <div class="event-card">
            <h3>Beneficiaries</h3>

            <div class="filter-section">
                <form method="GET" action="{{ route('staff.relief.show', $event->id) }}">
                    <select name="barangay_id" onchange="this.form.submit()" class="filter-select">
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

            <div class="scroll-table-wrapper">
                <table class="data-table">
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
                        @forelse($beneficiaries as $i => $beneficiary)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $beneficiary->beneficiary->first_name }} {{ $beneficiary->beneficiary->last_name }}</td>
                            <td>{{ $beneficiary->beneficiary->barangay->name ?? 'N/A' }}</td>
                            <td>{{ $beneficiary->beneficiary->family_size }}</td>
                            <td>
                                <span class="badge-vulnerability {{ strtolower($beneficiary->beneficiary->vulnerability_level ?? 'medium') }}">
                                    {{ $beneficiary->beneficiary->vulnerability_level ?? 'Medium' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="empty-row">No verified beneficiaries found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>

</div>
@endsection

@push('styles')
<style>
/* ─── Layout ─────────────────────────────────────────── */
.relief-event-container {
    display: grid;
    grid-template-columns: 1fr 1.6fr;
    gap: 1.5rem;
    padding: 0 1rem;
}

.left-column,
.right-column {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    min-width: 0;
}

/* ─── Header ─────────────────────────────────────────── */
.dash-header-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

/* ─── Cards ──────────────────────────────────────────── */
.event-card {
    background: #fff;
    border: 1px solid #d3d1c7;
    border-radius: 10px;
    padding: 1.25rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    min-width: 0;
}

.event-card h3 {
    margin: 0 0 1rem 0;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #2c2c2a;
}

/* ─── Info Table ─────────────────────────────────────── */
.info-table {
    width: 100%;
    border-collapse: collapse;
}

.info-table td {
    padding: 6px 0;
    font-size: 13px;
    vertical-align: top;
}

.info-table .label {
    font-weight: 600;
    color: #666;
    width: 110px;
    white-space: nowrap;
}

/* ─── Facilitators ───────────────────────────────────── */
.facilitator-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.facilitator-item:last-child { border-bottom: none; }

.facilitator-name {
    font-weight: 500;
    font-size: 13px;
}

.role-tag {
    background: #e8f4fd;
    color: #0066cc;
    padding: 2px 10px;
    border-radius: 12px;
    font-size: 12px;
    white-space: nowrap;
}

/* ─── Barangays ──────────────────────────────────────── */
.barangay-item {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.barangay-item:last-child { border-bottom: none; }

.barangay-name {
    font-weight: 600;
    font-size: 13px;
    margin-bottom: 2px;
}

.municipality-name {
    font-size: 12px;
    color: #666;
}

/* ─── Scrollable table wrapper ───────────────────────── */
.scroll-table-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
    border-radius: 6px;
}

/* ─── Data Tables ────────────────────────────────────── */
.data-table {
    width: 100%;
    min-width: 480px;
    border-collapse: collapse;
    font-size: 13px;
}

.data-table th {
    background: #f8f9fa;
    padding: 10px 12px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    white-space: nowrap;
}

.data-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}

.data-table tr:last-child td { border-bottom: none; }

/* ─── Filter ─────────────────────────────────────────── */
.filter-section { margin-bottom: 1rem; }

.filter-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d3d1c7;
    border-radius: 6px;
    font-size: 13px;
    background: #fff;
}

/* ─── Badges ─────────────────────────────────────────── */
.relief-status-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.relief-status-badge.ongoing  { background: #fef3c7; color: #d97706; }
.relief-status-badge.upcoming { background: #dbeafe; color: #1e40af; }
.relief-status-badge.done     { background: #d1fae5; color: #059669; }

.badge-vulnerability {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-vulnerability.low    { background: #d1fae5; color: #059669; }
.badge-vulnerability.medium { background: #fef3c7; color: #d97706; }
.badge-vulnerability.high   { background: #fee2e2; color: #dc2626; }

/* ─── Status Button ──────────────────────────────────── */
.btn-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    color: #fff;
    transition: opacity 0.2s;
}

.btn-status:hover { opacity: 0.85; }
.btn-ongoing { background: #10b981; }
.btn-default { background: #6b7280; }

/* ─── Empty states ───────────────────────────────────── */
.empty-message {
    font-size: 13px;
    color: #888;
    margin: 0;
}

.empty-row {
    text-align: center;
    color: #888;
    padding: 16px;
    font-style: italic;
}

/* ─── Responsive ─────────────────────────────────────── */
@media (max-width: 900px) {
    .relief-event-container {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .left-column,
    .right-column {
        gap: 1rem;
    }
}

@media (max-width: 600px) {
    .relief-event-container {
        padding: 0 0.5rem;
        gap: 0.75rem;
    }

    .left-column,
    .right-column {
        gap: 0.75rem;
    }

    .event-card {
        padding: 1rem;
    }

    .event-card h3 {
        font-size: 12px;
    }

    .info-table td {
        font-size: 12px;
    }

    .info-table .label {
        width: 90px;
    }

    .facilitator-item {
        flex-wrap: wrap;
    }

    .data-table {
        min-width: 420px;
        font-size: 12px;
    }

    .data-table th,
    .data-table td {
        padding: 8px;
    }

    .dash-header-actions {
        flex-wrap: wrap;
        gap: 8px;
    }
}
</style>
@endpush

@push('scripts')
<script>
function toggleEventStatus(eventId, currentStatus) {
    if (!confirm('Are you sure you want to change the event status?')) return;

    const btn = document.querySelector('.btn-status');
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    btn.disabled = true;

    const newStatus = currentStatus === 'Ongoing' ? 'Done' : 'Ongoing';

    fetch(`/staff/relief/${eventId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error updating status: ' + data.message);
            btn.innerHTML = originalContent;
            btn.disabled = false;
        }
    })
    .catch(() => {
        alert('Error updating status');
        btn.innerHTML = originalContent;
        btn.disabled = false;
    });
}

function exportPdf(eventId) {
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = `{{ route('staff.relief.pdf', ':id') }}`.replace(':id', eventId);
    form.style.display = 'none';

    [['paper_size', document.getElementById('paperSize').value], ['orientation', document.getElementById('orientation').value]].forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    closePdfDropdown('pdfOptions');
}
</script>
@endpush