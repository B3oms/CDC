@extends('staff.layouts.app')
@section('title', 'Relief Event Details')
@section('breadcrumb', 'Relief Event Details')

@section('content')
<div class="dash-header">
    <div style="text-align: left !important;">
        <h1 style="text-align: left !important;">{{ $event->name }}</h1>
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
        <a href="{{ route('relief.event.pdf', $event->id) }}" class="btn-export-pdf" target="_blank"
           style="display: inline-flex !important; align-items: center !important; gap: 6px !important; padding: 8px 16px !important; background: #10b981 !important; color: white !important; text-decoration: none !important; border-radius: 6px !important; font-size: 13px !important; font-weight: 500 !important; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3) !important; letter-spacing: 0.5px !important;"
           onmouseover="this.style.background='#059669'"
           onmouseout="this.style.background='#10b981'">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('staff.relief.index') }}" class="btn-back">← Back</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div style="width: 100%; max-width: 100%; margin: 0; padding: 0; box-sizing: border-box;">
    
    {{-- Event Info --}}
    <div style="background: #fff; border: 1px solid #d3d1c7; border-radius: 10px; padding: 16px; margin-bottom: 16px; width: 100%; box-sizing: border-box;">
        <h3 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 700; text-transform: uppercase;">Event Details</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr><td style="padding: 4px 0; font-weight: 600; color: #666; width: 120px;">Name</td><td style="padding: 4px 0;">{{ $event->name }}</td></tr>
            <tr><td style="padding: 4px 0; font-weight: 600; color: #666;">Date</td><td style="padding: 4px 0;">{{ is_string($event->date) ? date('M d, Y', strtotime($event->date)) : \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</td></tr>
            <tr><td style="padding: 4px 0; font-weight: 600; color: #666;">Venue</td><td style="padding: 4px 0;">{{ $event->venue }}</td></tr>
            <tr><td style="padding: 4px 0; font-weight: 600; color: #666;">Status</td><td style="padding: 4px 0;"><span class="relief-status-badge {{ strtolower($event->status) }}">{{ $event->status }}</span></td></tr>
            @if($event->calamity)
            <tr><td style="padding: 4px 0; font-weight: 600; color: #666;">Calamity</td><td style="padding: 4px 0;">{{ $event->calamity->name }}</td></tr>
            @endif
        </table>
    </div>

    {{-- Facilitators --}}
    <div style="background: #fff; border: 1px solid #d3d1c7; border-radius: 10px; padding: 16px; margin-bottom: 16px; width: 100%; box-sizing: border-box;">
        <h3 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 700; text-transform: uppercase;">Facilitators</h3>
        @forelse($event->facilitators as $f)
        <div style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
            </table>
        </div>

        {{-- Facilitators --}}
        <div class="event-card">
            <h3>Facilitators</h3>
            @forelse($event->facilitators as $f)
            <div class="facilitator-item">
                <span class="name">{{ $f->first_name }} {{ $f->last_name }}</span>
                <span class="role">{{ $f->role->name }}</span>
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
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Total Quantity</th>
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

            {{-- Barangay Filter --}}
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

            <div class="table-container">
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
                                <span class="badge-intensity {{ strtolower($beneficiary->beneficiary->vulnerability_level ?? 'medium') }}">
                                    {{ $beneficiary->beneficiary->vulnerability_level ?? 'Medium' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="empty-row">
                                No verified beneficiaries found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

                    </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function handleResponsive() {
        const screenWidth = window.innerWidth;
        const dashGrid = document.querySelector('.dash-grid');
        const yearlyCol = document.querySelector('.yearly-col');
        const rightCol = document.querySelector('.right-col');
        const sectionCards = document.querySelectorAll('.section-card');
        
        if (screenWidth <= 768) {
            // Mobile styles
            if (dashGrid) {
                dashGrid.style.display = 'grid';
                dashGrid.style.gridTemplateColumns = '1fr';
                dashGrid.style.gap = '1rem';
                dashGrid.style.width = '100%';
                dashGrid.style.margin = '0';
                dashGrid.style.padding = '0';
            }
            
            if (yearlyCol) {
                yearlyCol.style.display = 'flex';
                yearlyCol.style.flexDirection = 'column';
                yearlyCol.style.gap = '1rem';
                yearlyCol.style.width = '100%';
            }
            
            if (rightCol) {
                rightCol.style.display = 'flex';
                rightCol.style.flexDirection = 'column';
                rightCol.style.gap = '1rem';
                rightCol.style.width = '100%';
            }
            
            sectionCards.forEach(card => {
                card.style.background = '#fff';
                card.style.border = '1px solid #d3d1c7';
                card.style.borderRadius = '10px';
                card.style.padding = '1rem';
                card.style.marginBottom = '0';
                card.style.width = '100%';
                card.style.maxWidth = '100%';
                card.style.boxSizing = 'border-box';
                card.style.position = 'relative';
                card.style.zIndex = '1';
                card.style.overflow = 'visible';
            });
            
        } else if (screenWidth <= 480) {
            // Very small mobile styles
            if (dashGrid) {
                dashGrid.style.gap = '0.75rem';
                dashGrid.style.padding = '0 0.5rem';
            }
            
            if (yearlyCol) {
                yearlyCol.style.gap = '0.75rem';
            }
            
            if (rightCol) {
                rightCol.style.gap = '0.75rem';
            }
            
            sectionCards.forEach(card => {
                card.style.padding = '0.75rem';
                card.style.overflow = 'hidden';
            });
        }
    }
    
    // Initial call
    handleResponsive();
    
    // Handle resize
    window.addEventListener('resize', handleResponsive);
});
</script>
@endpush

@push('styles')
<style>
/* Relief Event Details - Responsive Layout */
.relief-event-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1rem;
}

.left-column, .right-column {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.event-card {
    background: #fff;
    border: 1px solid #d3d1c7;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.event-card h3 {
    margin: 0 0 1rem 0;
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    color: #2c2c2a;
}

/* Event Details Table */
.event-table {
    width: 100%;
    border-collapse: collapse;
}

.event-table .label {
    padding: 6px 0;
    font-weight: 600;
    color: #666;
    width: 120px;
    vertical-align: top;
}

.event-table td:not(.label) {
    padding: 6px 0;
    color: #333;
}

/* Facilitators */
.facilitator-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.facilitator-item:last-child {
    border-bottom: none;
}

.facilitator-item .name {
    font-weight: 500;
}

.facilitator-item .role {
    background: #e8f4fd;
    color: #0066cc;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
}

/* Barangays */
.barangay-item {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.barangay-item:last-child {
    border-bottom: none;
}

.barangay-name {
    font-weight: 600;
    margin-bottom: 2px;
}

.municipality-name {
    font-size: 12px;
    color: #666;
}

/* Data Tables */
.table-container {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.data-table {
    width: 100%;
    min-width: 500px;
    border-collapse: collapse;
}

.data-table th {
    background: #f8f9fa;
    padding: 10px;
    text-align: left;
    border-bottom: 2px solid #dee2e6;
    font-size: 12px;
    font-weight: 600;
    color: #495057;
}

.data-table td {
    padding: 10px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 13px;
}

.data-table tr:hover {
    background: #f8f9fa;
}

/* Filter and Export */
.filter-section {
    margin-bottom: 1rem;
}

.filter-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d3d1c7;
    border-radius: 6px;
    font-size: 13px;
    background: #fff;
}

.export-section {
    margin-top: 1rem;
    text-align: right;
}

.export-btn {
    display: inline-block;
    padding: 8px 16px;
    background: #1a3d1f;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-size: 13px;
    transition: background 0.2s;
}

.export-btn:hover {
    background: #2a5d2f;
}

/* Empty states */
.empty-message {
    font-size: 12px;
    color: #888;
    margin: 0;
}

.empty-row {
    text-align: center;
    color: #888;
    padding: 16px;
    font-style: italic;
}

/* Responsive Breakpoints */
@media (max-width: 1200px) {
    .relief-event-container {
        max-width: 1200px;
        gap: 1.25rem;
    }
    
    .event-card {
        padding: 1.25rem;
    }
}

@media (max-width: 992px) {
    .relief-event-container {
        grid-template-columns: 1fr;
        max-width: 800px;
        gap: 1rem;
    }
    
    .left-column, .right-column {
        gap: 1rem;
    }
    
    .event-card {
        padding: 1rem;
    }
    
    .event-card h3 {
        font-size: 13px;
    }
}

@media (max-width: 768px) {
    .relief-event-container {
        padding: 0 0.75rem;
        gap: 0.75rem;
    }
    
    .event-card {
        padding: 0.75rem;
    }
    
    .event-card h3 {
        font-size: 12px;
        margin-bottom: 0.75rem;
    }
    
    .data-table {
        min-width: 400px;
    }
    
    .data-table th, .data-table td {
        padding: 8px;
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .relief-event-container {
        padding: 0 0.5rem;
        gap: 0.5rem;
    }
    
    .event-card {
        padding: 0.5rem;
    }
    
    .event-card h3 {
        font-size: 11px;
        margin-bottom: 0.5rem;
    }
    
    .event-table .label {
        width: 100px;
        font-size: 11px;
    }
    
    .event-table td:not(.label) {
        font-size: 11px;
    }
    
    .facilitator-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    
    .data-table {
        min-width: 350px;
    }
    
    .data-table th, .data-table td {
        padding: 6px;
        font-size: 11px;
    }
    
    .export-btn {
        width: 100%;
        text-align: center;
        padding: 10px;
    }
}

/* Status Badge */
.relief-status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

/* Mobile responsive styles */
@media (max-width: 768px) {
    .dash-grid {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
        width: 100%;
    }
    
    .yearly-col {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        width: 100%;
    }
    
    .right-col {
        width: 100%;
    }
    
    .section-card {
        padding: 1rem;
        margin-bottom: 0;
        background: #fff;
        border: 1px solid #d3d1c7;
        border-radius: 10px;
        position: relative;
        z-index: 1;
        width: 100%;
        box-sizing: border-box;
    }
    
    /* Ensure all cards fit within screen */
    .section-card * {
        max-width: 100%;
        box-sizing: border-box;
    }
    
    .section-card h3 {
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }
    
    /* Event Details Table */
    .dist-table {
        font-size: 0.8rem;
        width: 100%;
        border-collapse: collapse;
    }
    
    .dist-table td {
        padding: 0.5rem;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .dist-table td:first-child {
        font-weight: 600;
        color: #374151;
        width: 120px;
    }
    
    /* Partner items and facilitators */
    .partner-item {
        padding: 0.5rem 0;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .dot {
        width: 6px;
        height: 6px;
        flex-shrink: 0;
    }
    
    .role-tag {
        font-size: 0.7rem;
        padding: 2px 6px;
        margin-left: auto;
    }
    
    .hint {
        font-size: 0.75rem;
        color: #6b7280;
        margin-left: 0.5rem;
    }
    
    /* Distributed Items and Beneficiaries Tables */
    .distributed-items-card,
    .beneficiaries-card {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .distributed-items-card .dist-table,
    .beneficiaries-card .dist-table {
        width: 100%;
        min-width: 600px;
    }
    
    /* Responsive table adjustments */
    @media (max-width: 1200px) {
        .distributed-items-card .dist-table,
        .beneficiaries-card .dist-table {
            min-width: 550px;
        }
    }
    
    @media (max-width: 992px) {
        .distributed-items-card .dist-table,
        .beneficiaries-card .dist-table {
            min-width: 500px;
        }
    }
    
    @media (max-width: 768px) {
        .distributed-items-card .dist-table,
        .beneficiaries-card .dist-table {
            min-width: 450px;
            font-size: 0.8rem;
        }
        
        .distributed-items-card .dist-table th,
        .distributed-items-card .dist-table td,
        .beneficiaries-card .dist-table th,
        .beneficiaries-card .dist-table td {
            padding: 0.4rem 0.3rem;
        }
    }
    
    .dist-table th {
        padding: 0.5rem;
        font-size: 0.75rem;
        background: #f8faf8;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .dist-table td {
        padding: 0.5rem;
        font-size: 0.75rem;
    }
    
    /* Fix beneficiaries table column widths */
    .beneficiaries-card .dist-table th:first-child,
    .beneficiaries-card .dist-table td:first-child {
        width: 40px;
        text-align: center;
        padding: 0.5rem 0.25rem;
    }
    
    .beneficiaries-card .dist-table th:nth-child(2),
    .beneficiaries-card .dist-table td:nth-child(2) {
        min-width: 120px;
        max-width: 180px;
        padding-left: 0.75rem;
    }
    
    .beneficiaries-card .dist-table th:nth-child(3),
    .beneficiaries-card .dist-table td:nth-child(3) {
        min-width: 100px;
        max-width: 120px;
        padding-left: 0.5rem;
    }
    
    /* Form controls */
    select {
        font-size: 0.8rem !important;
        padding: 0.5rem !important;
        width: 100%;
        margin-bottom: 0.75rem;
    }
    
    .btn-primary {
        font-size: 0.8rem !important;
        padding: 0.5rem 1rem !important;
        width: auto;
        margin-top: 0.5rem;
    }
    
    /* Make tables horizontally scrollable */
    .distributed-items-card,
    .beneficiaries-card {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .distributed-items-card .dist-table,
    .beneficiaries-card .dist-table {
        min-width: 600px;
    }
}

@media (max-width: 480px) {
    .dash-grid {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 0.75rem !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 0.5rem !important;
    }
    
    .yearly-col {
        display: flex !important;
        flex-direction: column !important;
        gap: 0.75rem !important;
        width: 100% !important;
    }
    
    .right-col {
        display: flex !important;
        flex-direction: column !important;
        gap: 0.75rem !important;
        width: 100% !important;
    }
    
    .section-card {
        background: #fff !important;
        border: 1px solid #d3d1c7 !important;
        border-radius: 10px !important;
        padding: 0.75rem !important;
        margin-bottom: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
        position: relative !important;
    }
    
    /* Ensure all content fits within screen */
    .section-card * {
        max-width: 100% !important;
        box-sizing: border-box !important;
        word-wrap: break-word !important;
    }
    
    /* Make tables responsive */
    .dist-table {
        width: 100% !important;
        max-width: 100% !important;
        table-layout: fixed !important;
        overflow-x: auto !important;
    }
    
    .section-card table {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
    }
    
    .section-card h3 {
        font-size: 0.9rem;
    }
    
    .dist-table {
        font-size: 0.75rem;
    }
    
    .dist-table td {
        padding: 0.4rem;
    }
    
    .dist-table td:first-child {
        width: 100px;
        font-size: 0.7rem;
    }
    
    .partner-item {
        font-size: 0.8rem;
        padding: 0.4rem 0;
    }
    
    .role-tag {
        font-size: 0.65rem;
        padding: 1px 4px;
    }
    
    .hint {
        font-size: 0.7rem;
        display: block;
        margin-left: 1rem;
        margin-top: 0.2rem;
    }
    
    .dist-table th {
        padding: 0.4rem;
        font-size: 0.7rem;
    }
    
    .dist-table td {
        padding: 0.4rem;
        font-size: 0.7rem;
    }
    
    select {
        font-size: 0.75rem !important;
        padding: 0.4rem !important;
    }
    
    .btn-primary {
        font-size: 0.75rem !important;
        padding: 0.4rem 0.8rem !important;
        width: auto;
    }
    
    /* Make tables horizontally scrollable on small screens */
    .distributed-items-card,
    .beneficiaries-card {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .distributed-items-card .dist-table,
    .beneficiaries-card .dist-table {
        width: 100%;
        min-width: 400px;
        font-size: 0.75rem;
    }
    
    .distributed-items-card .dist-table th,
    .distributed-items-card .dist-table td,
    .beneficiaries-card .dist-table th,
    .beneficiaries-card .dist-table td {
        padding: 0.3rem 0.2rem;
        font-size: 0.7rem;
    }
    
    /* Hide less important columns on very small screens */
    @media (max-width: 400px) {
        .beneficiaries-card .dist-table th:nth-child(5),
        .beneficiaries-card .dist-table td:nth-child(5) {
            display: none;
        }
        
        .distributed-items-card .dist-table th:nth-child(4),
        .distributed-items-card .dist-table td:nth-child(4) {
            display: none;
        }
    }
}

.relief-status-badge {
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

