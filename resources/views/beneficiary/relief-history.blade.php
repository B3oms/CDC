@extends('beneficiary.layouts.app')

@section('title', 'Relief History')
@section('breadcrumb', 'Relief History')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/barangay-system.css') }}">
<style>
/* Relief History Styles */
.relief-history-page {
    max-width: 100%;
    text-align: center;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
}

.beneficiary-badge {
    background: #eaf3de;
    border: 1px solid #1a6b2a;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    color: #1a6b2a;
    font-weight: 500;
}

/* Relief Events List */
.relief-events {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    text-align: center;
}

.event-card {
    background: #fff;
    border: 1px solid #f3f4f6;
    text-align: center;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.event-card:hover {
    border-color: #e5e7eb;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.event-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.event-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.event-date {
    font-size: 0.875rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.event-venue {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.event-calamity {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fbbf24;
    border-radius: 6px;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    margin-bottom: 1rem;
}

.event-status {
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-completed {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #10b981;
}

.status-ongoing {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fbbf24;
}

.status-scheduled {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #3b82f6;
}

/* Items Section */
.items-section {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #f3f4f6;
    text-align: center;
}

.items-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.items-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.75rem;
    justify-content: center;
}

.item-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.75rem;
    text-align: center;
}

.item-name {
    font-size: 0.875rem;
    font-weight: 500;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.item-details {
    font-size: 0.75rem;
    color: #6b7280;
}

.item-value {
    font-size: 0.75rem;
    color: #1a6b2a;
    font-weight: 500;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6b7280;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #4b5563;
    margin-bottom: 0.5rem;
}

.empty-state p {
    font-size: 0.875rem;
    margin: 0;
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

.pagination {
    display: flex;
    gap: 0.5rem;
}

.pagination a {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    color: #6b7280;
    text-decoration: none;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.pagination a:hover {
    border-color: #1a6b2a;
    color: #1a6b2a;
}

.pagination .active {
    background: #1a6b2a;
    color: white;
    border-color: #1a6b2a;
}

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .event-header {
        flex-direction: column;
    }
    
    .items-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')
<div class="relief-history-page">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Relief History</h1>
        </div>
        <div class="beneficiary-badge">
            {{ $beneficiary->first_name }} {{ $beneficiary->last_name }}
        </div>
    </div>

    <!-- Relief Events List -->
    <div class="relief-events">
        @if($reliefHistory->isEmpty())
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No Relief History</h3>
                <p>You haven't participated in any relief events yet.</p>
            </div>
        @else
            @foreach($reliefHistory as $eventBeneficiary)
                <div class="event-card">
                    <div class="event-header">
                        <div>
                            <div class="event-title">
                                {{ $eventBeneficiary->reliefEvent->name }}
                            </div>
                            <div class="event-date">
                                <i class="fas fa-calendar"></i>
                                {{ \Carbon\Carbon::parse($eventBeneficiary->reliefEvent->date)->format('F j, Y') }}
                            </div>
                            @if($eventBeneficiary->reliefEvent->venue)
                                <div class="event-venue">
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ $eventBeneficiary->reliefEvent->venue }}
                                </div>
                            @endif
                        </div>
                        <div class="event-status status-{{ $eventBeneficiary->reliefEvent->status }}">
                            {{ $eventBeneficiary->reliefEvent->status }}
                        </div>
                    </div>

                    @if($eventBeneficiary->reliefEvent->calamity)
                        <div class="event-calamity">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $eventBeneficiary->reliefEvent->calamity->name }}
                        </div>
                    @endif

                    <!-- Items Distributed -->
                    <div class="items-section">
                        <div class="items-title">
                            <i class="fas fa-box"></i>
                            Items Received
                        </div>
                        @php
                            $distributedItems = $eventBeneficiary->reliefEvent->distributedItems
                                ->where('beneficiary_id', $beneficiary->id);
                        @endphp
                        
                        @if($distributedItems->isEmpty())
                            <div style="color: #6b7280; font-size: 0.875rem;">
                                No specific items recorded for this event.
                            </div>
                        @else
                            <div class="items-grid">
                                @foreach($distributedItems as $item)
                                    <div class="item-card">
                                        <div class="item-name">{{ $item->item->name }}</div>
                                        <div class="item-details">
                                            Quantity: {{ $item->quantity }}
                                            @if($item->item->unit)
                                                {{ $item->item->unit }}
                                            @endif
                                        </div>
                                        @if($item->item->estimated_value)
                                            <div class="item-value">
                                                ₱{{ number_format($item->item->estimated_value * $item->quantity, 2) }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            @if($reliefHistory->hasPages())
                <div class="pagination-wrapper">
                    {{ $reliefHistory->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
