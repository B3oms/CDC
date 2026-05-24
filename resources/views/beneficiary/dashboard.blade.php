@extends('beneficiary.layouts.app')

@section('title', 'Beneficiary Dashboard')
@section('breadcrumb', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/barangay-system.css') }}">
<style>
/* Beneficiary Dashboard Styles */
.beneficiary-dashboard {
    max-width: 100%;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.welcome-section {
    flex: 1;
}

.welcome-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.welcome-subtitle {
    color: #6b7280;
    font-size: 0.875rem;
}

.beneficiary-info {
    background: #eaf3de;
    border: 1px solid #1a6b2a;
    border-radius: 8px;
    padding: 1rem;
    min-width: 250px;
}

.beneficiary-name {
    font-weight: 600;
    color: #1a6b2a;
    margin-bottom: 0.25rem;
}

.beneficiary-id {
    font-size: 0.875rem;
    color: #6b7280;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.stat-icon {
    width: 48px;
    height: 48px;
    background: #1a6b2a;
    color: white;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 1rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.stat-label {
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Relief History */
.relief-history {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f3f4f6;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-icon {
    width: 32px;
    height: 32px;
    background: #1a6b2a;
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.relief-item {
    padding: 1rem;
    border: 1px solid #f3f4f6;
    border-radius: 8px;
    margin-bottom: 1rem;
    transition: border-color 0.2s ease;
}

.relief-item:hover {
    border-color: #e5e7eb;
}

.relief-date {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.relief-event {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.relief-calamity {
    font-size: 0.875rem;
    color: #1a6b2a;
    margin-bottom: 0.5rem;
}

.relief-items {
    font-size: 0.875rem;
    color: #6b7280;
}

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

.btn-view-all {
    background: #1a6b2a;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: background 0.2s ease;
}

.btn-view-all:hover {
    background: #27500a;
}

/* Interview Section Styles */
.interview-section {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.interview-completed,
.interview-pending {
    text-align: center;
    padding: 2rem;
}

.completed-icon,
.pending-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    margin: 0 auto 1.5rem;
}

.completed-icon {
    background: #dcfce7;
    color: #16a34a;
}

.pending-icon {
    background: #fef3c7;
    color: #d97706;
}

.interview-completed h3,
.interview-pending h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.interview-completed p,
.interview-pending p {
    color: #6b7280;
    margin-bottom: 1rem;
}

.completed-date {
    font-size: 0.875rem;
    color: #6b7280;
    font-style: italic;
}

.interview-requirements {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    margin: 1rem 0;
    text-align: left;
}

.interview-requirements h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.interview-requirements ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.interview-requirements li {
    padding: 0.25rem 0;
    color: #6b7280;
    font-size: 0.875rem;
}

.interview-requirements li:before {
    content: "•";
    color: #3b82f6;
    font-weight: bold;
    margin-right: 0.5rem;
}

.btn-start-interview {
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: background 0.2s ease;
}

.btn-start-interview:hover {
    background: #2563eb;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .beneficiary-info {
        min-width: auto;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .section-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
}
</style>
@endpush

@section('content')
<div class="beneficiary-dashboard">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="welcome-section">
            <h1 class="welcome-title">Welcome back, {{ $beneficiary->first_name }}!</h1>
            <p class="welcome-subtitle">Here's your relief assistance history</p>
        </div>
        <div class="beneficiary-info">
            <div class="beneficiary-name">{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</div>
            <div class="beneficiary-id">ID: {{ $beneficiary->user->unique_id ?? 'N/A' }}</div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-value">{{ $totalEvents }}</div>
            <div class="stat-label">Relief Events Participated</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-value">{{ $totalItemsReceived }}</div>
            <div class="stat-label">Items Received</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-value">
                @if(session('beneficiary_interview'))
                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                @else
                    <i class="fas fa-exclamation-circle" style="color: #f59e0b;"></i>
                @endif
            </div>
            <div class="stat-label">Interview Form</div>
        </div>
    </div>

    <!-- Interview Form Section -->
    <div class="interview-section">
        <div class="section-header">
            <div class="section-title">
                <div class="section-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                Beneficiary Interview Form
            </div>
            @if(!session('beneficiary_interview'))
                <a href="{{ route('beneficiary.interview.create') }}" class="btn-view-all">
                    Complete Interview
                </a>
            @else
                <a href="{{ route('beneficiary.interview.show') }}" class="btn-view-all">
                    View Interview
                </a>
            @endif
        </div>

        @if(session('beneficiary_interview'))
            <div class="interview-completed">
                <div class="completed-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>Interview Completed</h3>
                <p>Your beneficiary interview form has been successfully submitted and reviewed.</p>
                <div class="completed-date">
                    Completed on: {{ session('beneficiary_interview.interview_date') }}
                </div>
            </div>
        @else
            <div class="interview-pending">
                <div class="pending-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>Interview Form Required</h3>
                <p>Please complete the beneficiary interview form to provide your family background information.</p>
                <div class="interview-requirements">
                    <h4>Required Information:</h4>
                    <ul>
                        <li>Mother's details (name, age, sex, birthdate)</li>
                        <li>Father's details (name, age, sex, birthdate)</li>
                        <li>Children's information (name, age, sex, birthdate)</li>
                        <li>Spouse information (if applicable)</li>
                    </ul>
                </div>
                <a href="{{ route('beneficiary.interview.create') }}" class="btn-start-interview">
                    <i class="fas fa-edit"></i> Start Interview
                </a>
            </div>
        @endif
    </div>

    <!-- Recent Relief History -->
    <div class="relief-history">
        <div class="section-header">
            <div class="section-title">
                <div class="section-icon">
                    <i class="fas fa-history"></i>
                </div>
                Recent Relief History
            </div>
            @if($reliefEvents->count() > 5)
                <a href="{{ route('beneficiary.relief-history') }}" class="btn-view-all">
                    View All
                </a>
            @endif
        </div>

        @if($reliefEvents->isEmpty())
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No Relief History</h3>
                <p>You haven't participated in any relief events yet.</p>
            </div>
        @else
            @php
                $recentEvents = $reliefEvents->take(5);
            @endphp
            
            @foreach($recentEvents as $eventBeneficiary)
                <div class="relief-item">
                    <div class="relief-date">
                        {{ \Carbon\Carbon::parse($eventBeneficiary->reliefEvent->date)->format('F j, Y') }}
                    </div>
                    <div class="relief-event">
                        {{ $eventBeneficiary->reliefEvent->name }}
                    </div>
                    @if($eventBeneficiary->reliefEvent->calamity)
                        <div class="relief-calamity">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $eventBeneficiary->reliefEvent->calamity->name }}
                        </div>
                    @endif
                    <div class="relief-items">
                        @php
                            $eventItems = $eventBeneficiary->reliefEvent->distributedItems
                                ->where('beneficiary_id', $beneficiary->id);
                            $itemCount = $eventItems->sum('quantity');
                            $itemTypes = $eventItems->pluck('item.name')->unique()->count();
                        @endphp
                        @if($itemCount > 0)
                            {{ $itemCount }} items ({{ $itemTypes }} types)
                        @else
                            Items recorded separately
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
