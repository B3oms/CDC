@extends('admin.layouts.app')
@section('title', 'Barangay Dashboard')

@section('content')
<div class="dash-header">
    <h1>Hello, {{ auth()->user()->first_name }}!</h1>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($activeCalamity)
<div class="cal-alert">
    <i class="fas fa-info-circle"></i>
    <strong>Active Calamity:</strong> {{ $activeCalamity->name }}
    <span class="badge-intensity {{ strtolower($activeCalamity->intensity) }}">{{ $activeCalamity->intensity }}</span>
</div>

<div class="forms-grid">

    {{-- Evacuation Center Form --}}
    <div class="form-card">
        <div class="form-card-section-label">EVACUATION CENTER</div>
        <form method="POST" action="{{ route('barangay.setCenter') }}">
            @csrf
            <input type="hidden" name="calamity_id" value="{{ $activeCalamity->id }}">

            <div class="field-group">
                <label class="field-label">Venue <span class="required">*</span></label>
                <input type="text" name="venue"
                       value="{{ $evacuationCenter->venue ?? '' }}"
                       placeholder="e.g. Barangay Hall, Community Center"
                       required class="field-input">
            </div>

            <div class="field-group">
                <label class="field-label">Location <span class="required">*</span></label>
                <input type="text" name="location"
                       value="{{ $evacuationCenter->location ?? '' }}"
                       placeholder="e.g. Purok 3, Sumacab Este"
                       required class="field-input">
            </div>

            <button type="submit" class="btn-submit {{ $evacuationCenter ? 'green' : '' }}">
    {{ $evacuationCenter ? 'Update Center' : 'Set Center' }}
</button>
        </form>
    </div>

    {{-- Report Form --}}
    @if($evacuationCenter)
    <div class="form-card">
        <div class="form-card-section-label">SUBMIT REPORT</div>
        <form method="POST" action="{{ route('barangay.submitReport') }}">
            @csrf
            <input type="hidden" name="calamity_id" value="{{ $activeCalamity->id }}">
            <input type="hidden" name="evacuation_center_id" value="{{ $evacuationCenter->id }}">

            <div class="field-group">
                <label class="field-label">Select Households in Evacuation</label>
                <div class="custom-select" id="householdDropdown">
                    <button type="button" class="select-trigger" onclick="toggleHouseholdSelector()">
                        <span id="trigger-text">Select households</span>
                        <span class="select-meta">
                            <span class="select-count" id="selected-count" style="display:none;">0 selected</span>
                            <span class="select-available">{{ $households ? $households->count() : 0 }} available</span>
                            <i class="fas fa-chevron-down select-arrow" id="dropdown-arrow"></i>
                        </span>
                    </button>
                    <div class="select-panel" id="household-selector-content">
                        <div class="select-panel-header">
                            <span>{{ $households ? $households->count() : 0 }} Registered Households</span>
                            <span>Selected: <strong id="selected-count-inner">0</strong></span>
                        </div>
                        <div class="select-list">
                            @if($households && $households->count() > 0)
                                @foreach($households as $household)
                                <label class="select-item">
                                    <input type="checkbox"
                                           name="household_ids[]"
                                           value="{{ $household->id }}"
                                           data-family-size="{{ $household->family_size }}"
                                           onchange="updateSelectedCount()">
                                    <span class="select-item-check"></span>
                                    <span class="select-item-info">
                                        <span class="select-item-name">{{ $household->head_of_household }}</span>
                                        <span class="select-item-meta">{{ $household->family_size }} members</span>
                                    </span>
                                </label>
                                @endforeach
                            @else
                                <div class="select-empty">
                                    <p>No approved households found for your barangay.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="field-group">
                <label class="field-label">Total Evacuees <span class="field-note">(Auto-calculated)</span></label>
                <input type="number" name="evacuee_count" id="evacuee-count"
                       min="0" readonly
                       value="{{ $latestReport->evacuee_count ?? 0 }}"
                       required class="field-input readonly">
                <span class="field-hint">Calculated from family sizes of selected households</span>
            </div>

            <button type="submit" class="btn-submit green">
                @if($latestReport)
                    Update Report
                @else
                    Submit Report
                @endif
            </button>
        </form>
    </div>

    @else
    <div class="form-card disabled-card">
        <div class="form-card-section-label">SUBMIT REPORT</div>
        <div class="disabled-body">
            <p>Set an evacuation center first before submitting reports.</p>
        </div>
    </div>
    @endif

</div>

@else
<div class="form-card" style="text-align:center; padding: 3rem 2rem;">
    <p style="color:#888; font-size:1rem; margin:0;">No active calamity at the moment.</p>
    <p style="color:#aaa; font-size:0.875rem; margin-top:6px;">You will be notified when the CDC opens one.</p>
</div>
@endif

@push('styles')
<style>
/* ── Variables ───────────────────────────── */
:root {
    --green:        #1a3d1f;
    --green-mid:    #1a6b2a;
    --green-light:  #eaf3de;
    --yellow:       #ef9f27;
    --yellow-light: #fffbeb;
    --border:       #e5e7eb;
    --radius:       8px;
    --text:         #1f2937;
    --muted:        #6b7280;
    --bg:           #f9fafb;
}

/* ── Alert ───────────────────────────────── */
.cal-alert {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--yellow-light);
    border: 1px solid #fde68a;
    border-radius: var(--radius);
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    color: var(--text);
    margin-bottom: 1.5rem;
}

.cal-alert i { color: var(--yellow); }

/* ── Grid ────────────────────────────────── */
.forms-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
    align-items: start;
}

/* ── Card ────────────────────────────────── */
.form-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.5rem;
}

.form-card-section-label {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: var(--muted);
    text-transform: uppercase;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--border);
}

/* ── Fields ──────────────────────────────── */
.field-group {
    margin-bottom: 1rem;
}

.field-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text);
    margin-bottom: 0.35rem;
}

.field-note {
    font-size: 0.75rem;
    font-weight: 400;
    color: var(--muted);
}

.required { color: #dc2626; margin-left: 2px; }

.field-input {
    width: 100%;
    padding: 0.55rem 0.75rem;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-size: 0.875rem;
    color: var(--text);
    background: #fff;
    box-sizing: border-box;
    outline: none;
    transition: border-color 0.15s;
    -webkit-appearance: none;
}

.field-input:focus {
    border-color: var(--green-mid);
    box-shadow: 0 0 0 3px rgba(26,107,42,0.08);
}

.field-input.readonly {
    background: var(--bg);
    color: var(--muted);
    cursor: not-allowed;
}

.field-hint {
    display: block;
    font-size: 0.72rem;
    color: var(--muted);
    margin-top: 0.3rem;
}

/* ── Buttons ─────────────────────────────── */
.btn-submit {
    width: 100%;
    margin-top: 1.25rem;
    padding: 0.6rem 1rem;
    background: var(--yellow);
    color: #fff;
    border: none;
    border-radius: var(--radius);
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s;
}

.btn-submit:hover { background: #d4890f; }

.btn-submit.green { background: var(--green-mid); }
.btn-submit.green:hover { background: var(--green); }

/* ── Custom Select ───────────────────────── */
.custom-select { position: relative; }

.select-trigger {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.55rem 0.75rem;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-size: 0.875rem;
    color: var(--text);
    cursor: pointer;
    text-align: left;
    transition: border-color 0.15s;
    box-sizing: border-box;
}

.select-trigger:hover { border-color: #d1d5db; }

.select-trigger.open {
    border-color: var(--green-mid);
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
    box-shadow: 0 0 0 3px rgba(26,107,42,0.08);
}

.select-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}

.select-count {
    font-size: 0.72rem;
    background: var(--green-mid);
    color: #fff;
    padding: 1px 7px;
    border-radius: 10px;
    font-weight: 600;
}

.select-available {
    font-size: 0.75rem;
    color: var(--muted);
}

.select-arrow {
    font-size: 0.7rem;
    color: var(--muted);
    transition: transform 0.2s;
}

.select-arrow.rotated { transform: rotate(180deg); }

.select-panel {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid var(--green-mid);
    border-top: none;
    border-bottom-left-radius: var(--radius);
    border-bottom-right-radius: var(--radius);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    z-index: 50;
}

.select-panel-header {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0.75rem;
    font-size: 0.75rem;
    color: var(--muted);
    background: var(--bg);
    border-bottom: 1px solid var(--border);
}

.select-panel-header strong { color: var(--green-mid); }

.select-list {
    max-height: 200px;
    overflow-y: auto;
    padding: 0.25rem;
}

.select-list::-webkit-scrollbar { width: 4px; }
.select-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

.select-item {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.5rem 0.6rem;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.1s;
    user-select: none;
}

.select-item:hover { background: var(--green-light); }

.select-item input[type="checkbox"] { display: none; }

.select-item-check {
    width: 16px;
    height: 16px;
    border: 1.5px solid var(--border);
    border-radius: 4px;
    flex-shrink: 0;
    transition: all 0.15s;
    background: #fff;
    position: relative;
}

.select-item input[type="checkbox"]:checked ~ .select-item-check {
    background: var(--green-mid);
    border-color: var(--green-mid);
}

.select-item input[type="checkbox"]:checked ~ .select-item-check::after {
    content: '';
    position: absolute;
    left: 4px;
    top: 1px;
    width: 5px;
    height: 9px;
    border: 2px solid #fff;
    border-top: none;
    border-left: none;
    transform: rotate(45deg);
}

.select-item:has(input:checked) { background: var(--green-light); }

.select-item-info {
    display: flex;
    flex-direction: column;
    gap: 1px;
}

.select-item-name {
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--text);
}

.select-item-meta {
    font-size: 0.72rem;
    color: var(--muted);
}

.select-empty {
    padding: 1.25rem;
    text-align: center;
    font-size: 0.8rem;
    color: var(--muted);
}

/* ── Disabled Card ───────────────────────── */
.disabled-card { opacity: 0.55; }

.disabled-body {
    padding: 2rem 0;
    text-align: center;
    font-size: 0.875rem;
    color: var(--muted);
}

/* ── Badge Intensity ─────────────────────── */
.badge-intensity {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.badge-intensity.low      { background: #d4edda; color: #155724; }
.badge-intensity.medium   { background: var(--yellow-light); color: #92650a; }
.badge-intensity.high     { background: #f8d7da; color: #721c24; }
.badge-intensity.critical { background: #d1ecf1; color: #0c5460; }
.badge-intensity.unknown  { background: #f3f4f6; color: var(--muted); }

/* ── Responsive ──────────────────────────── */
@media (max-width: 768px) {
    .forms-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@push('scripts')
<script>
function toggleHouseholdSelector() {
    const panel   = document.getElementById('household-selector-content');
    const arrow   = document.getElementById('dropdown-arrow');
    const trigger = document.querySelector('.select-trigger');
    const isOpen  = panel.style.display === 'block';

    panel.style.display = isOpen ? 'none' : 'block';
    arrow.classList.toggle('rotated', !isOpen);
    trigger.classList.toggle('open', !isOpen);
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('input[name="household_ids[]"]:checked');
    const count   = checked.length;

    let total = 0;
    checked.forEach(cb => total += parseInt(cb.dataset.familySize) || 0);

    const badge = document.getElementById('selected-count');
    const inner = document.getElementById('selected-count-inner');

    if (badge) {
        badge.textContent = count + ' selected';
        badge.style.display = count > 0 ? 'inline-block' : 'none';
    }
    if (inner) inner.textContent = count;

    const input = document.getElementById('evacuee-count');
    if (input) input.value = total;
}

document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('householdDropdown');
    const panel    = document.getElementById('household-selector-content');
    const arrow    = document.getElementById('dropdown-arrow');
    const trigger  = document.querySelector('.select-trigger');

    if (dropdown && !dropdown.contains(e.target) && panel.style.display === 'block') {
        panel.style.display = 'none';
        arrow.classList.remove('rotated');
        trigger.classList.remove('open');
    }
});

document.addEventListener('DOMContentLoaded', updateSelectedCount);
</script>
@endpush

@endsection