@extends('admin.layouts.app')
@section('title', 'Barangay Dashboard')

@section('content')
<div class="dash-header">
    <h1>Hello, {{ auth()->user()->first_name }}!</h1>
    <span class="logo-circle" style="border-color:#1a3d1f;"></span>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($activeCalamity)
<div class="alert alert-info" style="margin-bottom:1.5rem;">
    <i class="fas fa-info-circle"></i>
    <strong>Active Calamity:</strong> {{ $activeCalamity->name }} 
    <span class="badge-intensity {{ strtolower($activeCalamity->intensity) }}">{{ $activeCalamity->intensity }}</span>
</div>

<div class="dash-grid" style="grid-template-columns: 1fr; gap: 1.5rem;">
    {{-- Forms Section --}}
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="dash-grid" style="grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            {{-- Evacuation Center Form --}}
            <div class="section-card" style="flex: 1; display: flex; flex-direction: column;">
                <h3>Evacuation Center</h3>
                <form method="POST" action="{{ route('barangay.setCenter') }}" style="flex: 1; display: flex; flex-direction: column;">
                    @csrf
                    <input type="hidden" name="calamity_id" value="{{ $activeCalamity->id }}">
                    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div class="form-group">
                                <label>Venue</label>
                                <input type="text" name="venue" value="{{ $evacuationCenter->venue ?? '' }}" 
                                       placeholder="e.g. Barangay Hall, Community Center" required>
                            </div>
                            <div class="form-group">
                                <label>Location</label>
                                <input type="text" name="location" value="{{ $evacuationCenter->location ?? '' }}" 
                                       placeholder="e.g. Purok 3, Sumacab Este" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary" style="width:100%;margin-top:auto;">
                            {{ $evacuationCenter ? 'Update Center' : 'Set Center' }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Report Form --}}
            @if($evacuationCenter)
            <div class="section-card" style="flex: 1; display: flex; flex-direction: column;">
                <h3>Submit Report</h3>
                <form method="POST" action="{{ route('barangay.submitReport') }}" style="flex: 1; display: flex; flex-direction: column;">
                    @csrf
                    <input type="hidden" name="calamity_id" value="{{ $activeCalamity->id }}">
                    <input type="hidden" name="evacuation_center_id" value="{{ $evacuationCenter->id }}">
                    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div class="form-group">
                                <label>Select Households in Evacuation</label>
                                <div class="collapsible-selector">
                                    <div class="selector-trigger" onclick="toggleHouseholdSelector()">
                                        <div class="trigger-content">
                                            <span class="trigger-icon">▼</span>
                                            <span class="trigger-text">
                                                🏠 Select Households 
                                                <span class="selected-badge" id="selected-count">0</span>
                                            </span>
                                            <span class="trigger-info">{{ $households ? $households->count() : 0 }} available</span>
                                        </div>
                                    </div>
                                    <div class="selector-content" id="household-selector-content" style="display: none;">
                                        <div class="selector-header">
                                            <span class="selector-info">
                                                📋 Registered Households: {{ $households ? $households->count() : 0 }}
                                            </span>
                                            <span class="selected-info">
                                                Selected: <strong id="selected-count-inner">0</strong>
                                            </span>
                                        </div>
                                        <div class="household-list">
                                            @if($households && $households->count() > 0)
                                                @foreach($households as $household)
                                                <label class="household-item">
                                                    <input type="checkbox" name="household_ids[]" value="{{ $household->id }}" 
                                                           data-family-size="{{ $household->family_size }}"
                                                           onchange="updateSelectedCount()">
                                                    <span class="household-info">
                                                        <span class="household-name">{{ $household->head_of_household }}</span>
                                                        <span class="family-size">({{ $household->family_size }} members)</span>
                                                    </span>
                                                </label>
                                                @endforeach
                                            @else
                                                <div style="padding: 20px; text-align: center; color: #6b7280;">
                                                    <p>No approved households found for your barangay.</p>
                                                    <p style="font-size: 0.875rem; margin-top: 8px;">Please contact the administrator if you believe this is an error.</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="selector-footer">
                                            <small class="selector-help">
                                                💡 Click households to select them for evacuation
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Total Evacuees (Auto-calculated)</label>
                                <div class="evacuees-display">
                                    <input type="number" name="evacuee_count" id="evacuee-count" min="0" readonly
                                           value="{{ $latestReport->evacuee_count ?? 0 }}" required>
                                    <small class="evacuees-info">
                                        📊 Calculated from family sizes of selected households
                                    </small>
                                </div>
                            </div>
                                                </div>
                        <button type="submit" class="btn-primary" style="width:100%;margin-top:auto;">
                            Update Report
                        </button>
                    </div>
                </form>
            </div>
            @else
            <div class="section-card" style="opacity:0.7; flex: 1; display: flex; flex-direction: column;">
                <h3>Submit Report</h3>
                <div style="flex: 1; display: flex; align-items: center; justify-content: center; color: #6b7280;">
                    <p>You must set an evacuation center before submitting reports.</p>
                </div>
            </div>
            @endif
        </div>

            </div>
</div>

@else
<div class="section-card" style="text-align:center;padding:3rem;">
    <p style="font-size:1.1rem;color:#888;">No active calamity at the moment.</p>
    <p style="font-size:0.9rem;color:#aaa;margin-top:8px;">You will be notified when the CDC opens one.</p>
</div>
@endif

@push('styles')
<style>
/* Collapsible Household Selector Styles */
.collapsible-selector {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    transition: border-color 0.2s ease;
}

.collapsible-selector:hover {
    border-color: #d1d5db;
}

.selector-trigger {
    padding: 12px 16px;
    background: #f9fafb;
    cursor: pointer;
    user-select: none;
    transition: background-color 0.2s ease;
}

.selector-trigger:hover {
    background: #f3f4f6;
}

.trigger-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.trigger-icon {
    font-size: 0.875rem;
    color: #6b7280;
    transition: transform 0.2s ease;
    margin-right: 8px;
}

.trigger-icon.rotated {
    transform: rotate(180deg);
}

.trigger-text {
    flex: 1;
    font-weight: 600;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 8px;
}

.selected-badge {
    background: #059669;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    min-width: 20px;
    text-align: center;
}

.trigger-info {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.selector-content {
    border-top: 1px solid #e5e7eb;
}

.selector-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}

.selector-info {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.selected-info {
    font-size: 0.875rem;
    color: #1f2937;
    font-weight: 600;
}

.selected-info strong {
    color: #059669;
    font-size: 1rem;
}

.household-list {
    max-height: 200px;
    overflow-y: auto;
    padding: 8px;
}

.household-item {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    margin: 2px 0;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.2s ease;
    border: 1px solid transparent;
}

.household-item:hover {
    background-color: #f3f4f6;
}

.household-item input[type="checkbox"] {
    margin-right: 12px;
    width: 16px;
    height: 16px;
    accent-color: #059669;
}

.household-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.household-name {
    font-weight: 500;
    color: #374151;
    font-size: 0.875rem;
}

.family-size {
    color: #6b7280;
    font-weight: 400;
    font-size: 0.75rem;
}

.selector-footer {
    padding: 8px 16px;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
}

.selector-help {
    color: #6b7280;
    font-size: 0.75rem;
    font-style: italic;
}

/* Evacuees Display Styling */
.evacuees-display {
    position: relative;
}

.evacuees-display input[readonly] {
    background: #f9fafb;
    cursor: not-allowed;
    border-color: #d1d5db;
}

.evacuees-info {
    color: #059669;
    font-size: 0.75rem;
    font-weight: 500;
    display: block;
    margin-top: 4px;
}

/* Enhanced form group styling */
.form-group label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    display: block;
}
</style>
@endpush

@push('scripts')
<script>
function toggleHouseholdSelector() {
    const content = document.getElementById('household-selector-content');
    const icon = document.querySelector('.trigger-icon');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.classList.add('rotated');
    } else {
        content.style.display = 'none';
        icon.classList.remove('rotated');
    }
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('input[name="household_ids[]"]:checked');
    const count = checkboxes.length;
    
    // Calculate total evacuees based on family sizes
    let totalEvacuees = 0;
    checkboxes.forEach(checkbox => {
        const familySize = parseInt(checkbox.dataset.familySize) || 0;
        totalEvacuees += familySize;
    });
    
    // Update both count displays
    const outerCount = document.getElementById('selected-count');
    const innerCount = document.getElementById('selected-count-inner');
    
    if (outerCount) outerCount.textContent = count;
    if (innerCount) innerCount.textContent = count;
    
    // Update evacuees count
    const evacueesInput = document.getElementById('evacuee-count');
    if (evacueesInput) {
        evacueesInput.value = totalEvacuees;
    }
    
    // Update selected info styling
    const selectedInfo = document.querySelector('.selected-info');
    if (selectedInfo) {
        if (count > 0) {
            selectedInfo.style.color = '#059669';
        } else {
            selectedInfo.style.color = '#1f2937';
        }
    }
    
    // Update trigger badge visibility
    const badge = document.querySelector('.selected-badge');
    if (badge) {
        if (count > 0) {
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initial count update
    updateSelectedCount();
    
    // Add click handlers to household items for better UX
    const householdItems = document.querySelectorAll('.household-item');
    householdItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Prevent toggling when clicking on checkbox directly
            if (e.target.type !== 'checkbox') {
                const checkbox = this.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    updateSelectedCount();
                }
            }
        });
    });
});
</script>
@endpush

@endsection