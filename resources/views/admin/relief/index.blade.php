@extends('admin.layouts.app')
@section('title', 'Relief Monitor')
@section('breadcrumb', 'Relief Monitor')

@section('content')
<div class="dash-header">
    <h1>Relief Monitor</h1>
    <div class="dash-header-actions">
        <a href="{{ route('admin.relief.create') }}" class="btn-primary">+ Create Event</a>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .dash-header {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .dash-header-actions {
        display: flex;
        justify-content: center;
        width: 100%;
    }
}
</style>

{{-- Stats Row --}}
<div class="stats-row" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-num" id="ongoingCount">{{ $ongoingCount ?? 0 }}</div>
        <div class="stat-label">Ongoing Events</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" id="upcomingCount">{{ $upcomingCount ?? 0 }}</div>
        <div class="stat-label">Upcoming Events</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" id="completedCount">{{ $completedCount ?? 0 }}</div>
        <div class="stat-label">Completed Events</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" id="totalBeneficiaries">{{ $totalBeneficiaries ?? 0 }}</div>
        <div class="stat-label">Total Beneficiaries</div>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($events->isEmpty())
<div class="section-card" style="text-align:center;padding:3rem;">
    <p style="color:#888;font-size:1rem;">No relief events yet.</p>
    <a href="{{ route('admin.relief.create') }}" class="btn-primary" style="margin-top:1rem;display:inline-block;">
        Create First Event
    </a>
</div>
@else

{{-- Ongoing --}}
@if($events->where('status', 'Ongoing')->count())
<div class="relief-section">
    <div class="relief-section-title">Ongoing</div>
    <div class="relief-grid">
        @foreach($events->where('status', 'Ongoing') as $event)
            @include('admin.relief.partials.card', ['event' => $event])
        @endforeach
    </div>
</div>
@endif

{{-- Upcoming --}}
@if($events->where('status', 'Upcoming')->count())
<div class="relief-section">
    <div class="relief-section-title">Upcoming</div>
    <div class="relief-grid">
        @foreach($events->where('status', 'Upcoming') as $event)
            @include('admin.relief.partials.card', ['event' => $event])
        @endforeach
    </div>
</div>
@endif

{{-- Done --}}
@if($events->where('status', 'Done')->count())
<div class="relief-section">
    <div class="relief-section-title">Completed</div>
    <div class="relief-grid">
        @foreach($events->where('status', 'Done') as $event)
            @include('admin.relief.partials.card', ['event' => $event])
        @endforeach
    </div>
</div>
@endif

@endif
@endsection

@push('scripts')
<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.stat-num {
    transition: all 0.3s ease;
}

.stat-num.updating {
    color: #10b981;
    transform: scale(1.1);
}

/* Delete Button Styles */
.relief-card-wrapper {
    position: relative;
}

.delete-form {
    position: absolute;
    top: 8px;
    right: 8px;
    z-index: 10;
}

/* Make space for delete button */
.relief-card-header {
    padding-right: 40px;
}

.delete-btn {
    background: transparent;
    border: none;
    color: #888;
    width: 24px;
    height: 24px;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    opacity: 0.6;
}

.delete-btn:hover {
    background: #f5f5f5;
    color: #ef4444;
    opacity: 1;
}

/* Mobile responsive styles for relief monitor */
@media (max-width: 768px) {
    .relief-section {
        margin-bottom: 2rem;
    }
    
    .relief-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .relief-card-wrapper {
        position: relative;
        width: 100%;
    }
    
    .relief-card {
        background: #fff;
        border: 1px solid #d3d1c7;
        border-radius: 10px;
        padding: 1rem;
        transition: border-color 0.2s, transform 0.15s;
        display: block;
        width: 100%;
        position: relative;
        z-index: 1;
    }
    
    .relief-card:hover {
        border-color: #1a3d1f;
        transform: translateY(-2px);
        z-index: 2;
    }
    
    .relief-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 0.75rem;
        padding-right: 40px;
    }
    
    .relief-card-name {
        font-weight: 700;
        font-size: 13px;
        color: #2c2c2a;
        line-height: 1.3;
    }
    
    .relief-card-meta {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-bottom: 0.75rem;
    }
    
    .meta-row {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
    }
    
    .meta-label {
        color: #888780;
        font-weight: 500;
    }
    
    .relief-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid #f3f4f6;
    }
    
    .view-details {
        font-size: 11px;
        color: #185fa5;
    }
}

@media (max-width: 480px) {
    .relief-section {
        margin-bottom: 1.5rem;
    }
    
    .relief-grid {
        gap: 1rem;
    }
    
    .relief-card {
        padding: 0.75rem;
    }
    
    .relief-card-name {
        font-size: 12px;
    }
    
    .meta-row {
        font-size: 10px;
        flex-direction: column;
        gap: 2px;
    }
    
    .relief-card-footer {
        flex-direction: column;
        gap: 0.5rem;
        align-items: stretch;
    }
    
    .view-details {
        text-align: center;
    }
}

/* Status Button Styles */
.status-button-container {
    flex: 1;
    display: flex;
    justify-content: center;
}

.status-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.status-btn.status-ongoing {
    background: #10b981;
    color: white;
}

.status-btn.status-ongoing:hover {
    background: #059669;
    transform: translateY(-1px);
}

.status-btn.status-finished {
    background: #6b7280;
    color: white;
}

.status-btn.status-finished:hover {
    background: #4b5563;
    transform: translateY(-1px);
}

.status-btn i {
    font-size: 14px;
}

.relief-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    border-top: 1px solid #f3f4f6;
}

.view-details {
    font-size: 12px;
    color: #6b7280;
    text-decoration: none;
}
</style>

<script>
let refreshInterval;
let isUpdating = false;

function updateStats() {
    if (isUpdating) return;
    isUpdating = true;
    
    fetch('{{ route("admin.relief.stats") }}')
        .then(response => response.json())
        .then(data => {
            // Update statistics with animation
            updateStatWithAnimation('ongoingCount', data.ongoingCount);
            updateStatWithAnimation('upcomingCount', data.upcomingCount);
            updateStatWithAnimation('completedCount', data.completedCount);
            updateStatWithAnimation('totalBeneficiaries', data.totalBeneficiaries);
            
            // Update last updated time
            document.getElementById('lastUpdated').textContent = data.lastUpdated;
            
            // Show update indicator
            showUpdateIndicator();
        })
        .catch(error => {
            console.error('Error fetching stats:', error);
        })
        .finally(() => {
            isUpdating = false;
        });
}

function updateStatWithAnimation(elementId, newValue) {
    const element = document.getElementById(elementId);
    const oldValue = parseInt(element.textContent);
    
    if (oldValue !== newValue) {
        element.classList.add('updating');
        
        // Animate number change
        animateValue(element, oldValue, newValue, 500);
        
        setTimeout(() => {
            element.classList.remove('updating');
        }, 500);
    }
}

function animateValue(element, start, end, duration) {
    const range = end - start;
    const increment = range > 0 ? 1 : -1;
    const stepTime = Math.abs(Math.floor(duration / range));
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        element.textContent = current;
        
        if (current === end) {
            clearInterval(timer);
        }
    }, stepTime);
}

function showUpdateIndicator() {
    const dot = document.querySelector('.realtime-dot');
    dot.style.background = '#10b981';
    dot.style.animation = 'none';
    
    setTimeout(() => {
        dot.style.animation = 'pulse 2s infinite';
    }, 100);
}

// Start real-time updates
function startRealTimeUpdates() {
    // Initial load
    updateStats();
    
    // Refresh every 30 seconds
    refreshInterval = setInterval(updateStats, 30000);
}

// Stop real-time updates when page is not visible
function handleVisibilityChange() {
    if (document.hidden) {
        clearInterval(refreshInterval);
    } else {
        startRealTimeUpdates();
    }
}

// Initialize real-time functionality
document.addEventListener('DOMContentLoaded', function() {
    startRealTimeUpdates();
    
    // Handle page visibility
    document.addEventListener('visibilitychange', handleVisibilityChange);
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        clearInterval(refreshInterval);
    });
});

// Manual refresh button (optional)
document.addEventListener('keydown', function(e) {
    // Press 'R' key to manually refresh
    if (e.key === 'r' && !e.ctrlKey && !e.metaKey && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
        e.preventDefault();
        updateStats();
    }
});
</script>
@endpush