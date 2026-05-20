@extends('admin.layouts.app')
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
        <div class="pdf-export-dropdown" style="position:relative;display:inline-block;">
            <button onclick="togglePdfDropdown(event)" class="btn-export-pdf"
               style="display: inline-flex !important; align-items: center !important; gap: 6px !important; padding: 8px 16px !important; background: #10b981 !important; color: white !important; text-decoration: none !important; border-radius: 6px !important; font-size: 13px !important; font-weight: 500 !important; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3) !important; letter-spacing: 0.5px !important; border:none !important; cursor:pointer !important;"
               onmouseover="this.style.background='#059669'"
               onmouseout="this.style.background='#10b981'">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
            <div id="pdfOptions" class="pdf-options" style="display:none;position:absolute;top:100%;right:0;background:white;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);padding:12px;min-width:200px;z-index:1001;">
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Paper Size</label>
                    <select id="paperSize" style="width:100%;padding:6px 8px;border:1px solid #d1d5db;border-radius:4px;font-size:13px;color:#374151;">
                        <option value="A4">A4</option>
                        <option value="Letter">Letter</option>
                        <option value="Legal">Legal</option>
                    </select>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Orientation</label>
                    <select id="orientation" style="width:100%;padding:6px 8px;border:1px solid #d1d5db;border-radius:4px;font-size:13px;color:#374151;">
                        <option value="portrait" selected>Portrait</option>
                        <option value="landscape">Landscape</option>
                    </select>
                </div>
                <button onclick="exportPdf({{ $event->id }})" style="width:100%;padding:8px;background:#10b981;color:white;border:none;border-radius:4px;font-size:13px;font-weight:500;cursor:pointer;transition:background 0.2s;"
                   onmouseover="this.style.background='#059669'"
                   onmouseout="this.style.background='#10b981'">
                    Export PDF
                </button>
            </div>
        </div>
        <a href="{{ route('admin.relief.index') }}" class="btn-back">← Back</a>
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
                @if($event->calamity)
                <tr><td class="label">Calamity</td><td>{{ $event->calamity->name }}</td></tr>
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
            @foreach($event->eventBarangays as $eb)
            <div class="barangay-item">
                <div class="barangay-name">{{ $eb->barangay->name }}</div>
                <div class="municipality-name">{{ $eb->municipality->name }}</div>
            </div>
            @endforeach
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
                <form method="GET" action="{{ route('admin.relief.show', $event->id) }}">
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
    min-width: 0; /* prevent grid blowout */
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
    min-width: 480px; /* forces scroll on small screens */
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
.data-table tr:hover { background: #f8f9fa; }

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

/* ─── Export ─────────────────────────────────────────── */
.export-section {
    margin-top: 1rem;
    text-align: right;
}

.export-btn {
    display: inline-block;
    padding: 8px 18px;
    background: #1a3d1f;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-size: 13px;
    transition: background 0.2s;
}

.export-btn:hover { background: #2a5d2f; }

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
.btn-ongoing  { background: #10b981; }
.btn-default  { background: #6b7280; }

.btn-export-pdf {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 8px 16px !important;
    background: #10b981 !important;
    color: white !important;
    text-decoration: none !important;
    border-radius: 6px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3) !important;
    letter-spacing: 0.5px !important;
}

.btn-export-pdf:hover {
    background: #059669 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 8px 12px -2px rgba(16, 185, 129, 0.4) !important;
    text-decoration: none !important;
    color: white !important;
}

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

/* Tablet: single column */
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

/* Mobile */
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

    .export-btn {
        width: 100%;
        text-align: center;
        padding: 10px;
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

    fetch(`/admin/relief/${eventId}/status`, {
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

// PDF Export Functions
let dropdownOpenTime = 0;

function togglePdfDropdown(event) {
    if (event) {
        event.stopPropagation();
    }
    const dropdown = document.getElementById('pdfOptions');
    if (dropdown.style.display === 'none') {
        dropdown.style.display = 'block';
        dropdownOpenTime = Date.now();
    } else {
        dropdown.style.display = 'none';
    }
}

function exportPdf(eventId) {
    const paperSize = document.getElementById('paperSize').value;
    const orientation = document.getElementById('orientation').value;
    const url = `{{ route('admin.relief.event.pdf', ':id') }}`.replace(':id', eventId);
    const fullUrl = `${url}?paper_size=${paperSize}&orientation=${orientation}`;
    window.open(fullUrl, '_blank');
    document.getElementById('pdfOptions').style.display = 'none';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('pdfOptions');
    const button = event.target.closest('.pdf-export-dropdown');
    const insideDropdown = event.target.closest('#pdfOptions');
    if (!button && !insideDropdown && dropdown && dropdown.style.display === 'block') {
        dropdown.style.display = 'none';
    }
});
</script>
@endpush