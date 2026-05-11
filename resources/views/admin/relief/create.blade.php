@extends('admin.layouts.app')
@section('title', 'Create Relief Event')
@section('breadcrumb', '<i class="fas fa-hand-holding-heart"></i> Relief Management / Create Event')

@section('content')

@if($calamityId)
<div class="alert alert-info" style="margin-bottom:1rem;">
    <i class="fas fa-info-circle"></i>
    <strong>Auto-filled from Calamity Portal.</strong>
    Top 5 most affected barangays have been pre-selected. Review and confirm before creating.
</div>
@endif

<div class="page-header">
    <div class="page-title">
        <h1>Create Relief Event</h1>
        <p class="page-description">Schedule and organize relief operations for affected barangays</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.relief.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Relief Events
        </a>
    </div>
</div>

<div class="form-card">

    @if($errors->any())
    <div class="alert-error">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.relief.store') }}">
        @csrf

        <div class="form-grid">

            <div class="form-row">
                <div class="form-group">
                    <label for="date">Date *</label>
                    <input type="date" id="date" name="date"
                        value="{{ old('date', $prefillDate ?? '') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="time">Time *</label>
                    <input type="time" id="time" name="time"
                        value="{{ old('time') }}" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="venue">Venue *</label>
                    <input type="text" id="venue" name="venue"
                        value="{{ old('venue') }}"
                        placeholder="e.g. San Fernando City Hall" required>
                </div>
                
                @if($calamityId)
                <div class="form-group">
                    <label for="calamity_id">Associated Calamity</label>
                    <select id="calamity_id" name="calamity_id">
                        <option value="{{ $calamityId }}" selected>Current Calamity</option>
                        <option value="">Remove Association</option>
                    </select>
                    <small class="form-help">This event is linked to an active calamity</small>
                </div>
                @else
                <div class="form-group">
                    <label for="calamity_id">Associate with Calamity (Optional)</label>
                    <select id="calamity_id" name="calamity_id">
                        <option value="">No Calamity</option>
                        @foreach($calamities ?? [] as $calamity)
                            <option value="{{ $calamity->id }}"
                                {{ old('calamity_id') == $calamity->id ? 'selected' : '' }}>
                                {{ $calamity->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-help">Optional: Associate with an existing calamity</small>
                </div>
                @endif
                
                @if($calamityId)
                <div class="form-group">
                    <label for="intensity">Intensity</label>
                    <select id="intensity" name="intensity">
                        <option value="">Use Calamity Intensity</option>
                        <option value="low" {{ old('intensity') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('intensity') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('intensity') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('intensity') == 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                    <small class="form-help">Override calamity intensity if needed</small>
                </div>
                @endif
            </div>

            <div class="form-group">
                <label>Event Name</label>
                <input type="text" name="name"
                    value="{{ old('name', $prefillName ?? '') }}"
                    placeholder="e.g. Relief Op - Sumacab Este" required>
            </div>

        </div>

        {{-- Facilitators --}}
        <div class="form-group full-width" style="margin-bottom:1rem;">
            <label>Facilitators</label>
            <p class="hint">Select staff, volunteers, or barangay representatives.</p>
            <div class="facilitator-list">
                @foreach($facilitators as $f)
                <label class="barangay-check">
                    <input type="checkbox"
                        name="facilitator_ids[]"
                        value="{{ $f->id }}"
                        {{ in_array($f->id, old('facilitator_ids', [])) ? 'checked' : '' }}>
                    {{ $f->first_name }} {{ $f->last_name }}
                    <span class="role-tag">{{ $f->role->name }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Barangays --}}
        <div class="form-group full-width">
            <label>Select Barangays</label>
            <p class="hint">Verified beneficiaries from selected barangays will be automatically added.</p>

            @foreach($municipalities as $municipality)
            <div class="municipality-group">
                <div class="municipality-header">
                    <input type="checkbox" class="select-all" data-municipality="{{ $municipality->id }}">
                    <strong>{{ $municipality->name }}, {{ $municipality->province }}</strong>
                </div>
                <div class="barangay-list">
                    @foreach($municipality->barangays as $barangay)
                    <label class="barangay-check">
                        <input type="checkbox"
                            name="barangay_ids[]"
                            value="{{ $barangay->id }}"
                            class="brgy-{{ $municipality->id }}"
                            {{ in_array($barangay->id, old('barangay_ids', $topBarangays)) ? 'checked' : '' }}>
                        {{ $barangay->name }}
                        <span class="hint" style="margin:0;">
                            ({{ \App\Models\Beneficiary::where('barangay_id', $barangay->id)->where('is_verified', 1)->count() }} verified beneficiaries)
                        </span>
                        @if(in_array($barangay->id, $topBarangays))
                            <span class="role-tag" style="background:#eaf3de;color:#3b6d11;">
                                Top {{ array_search($barangay->id, $topBarangays) + 1 }}
                            </span>
                        @endif
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Distribution Calculator --}}
<div class="form-group full-width" style="margin-top:1rem;">
    <label>Distribute Inventory Items</label>
    <p class="hint">Select items and enter total households to calculate equal distribution per household.</p>

    <div class="calc-box">
        <div class="calc-header">
            <div class="form-group" style="flex:1;">
                <label>Total Households</label>
                <input type="number" id="total-households" min="1" value="1"
                    placeholder="e.g. 50" oninput="calculateDistribution()">
            </div>
        </div>

        <div class="calc-items" id="calc-items">
            @foreach(\App\Models\Category::with('subcategories.items.inventory')->get() as $cat)
                @foreach($cat->subcategories as $sub)
                    @foreach($sub->items as $item)
                    <div class="calc-item-row">
                        <label class="calc-check">
                            <input type="checkbox"
                                name="distribute_items[]"
                                value="{{ $item->id }}"
                                data-quantity="{{ $item->inventory?->quantity ?? 0 }}"
                                data-name="{{ $item->name }}"
                                data-unit="{{ $item->unit }}"
                                onchange="calculateDistribution()">
                            <span class="calc-item-name">
                                {{ $item->name }}
                                <span class="hint" style="margin:0;">
                                    {{ $cat->name }} → {{ $sub->name }}
                                </span>
                            </span>
                        </label>
                        <div class="calc-stock">
                            Stock: <strong>{{ $item->inventory?->quantity ?? 0 }}</strong> {{ $item->unit }}
                        </div>
                        <div class="calc-result" id="result-{{ $item->id }}">
                            —
                        </div>
                    </div>
                    @endforeach
                @endforeach
            @endforeach
        </div>

        {{-- Summary --}}
        <div class="calc-summary" id="calc-summary" style="display:none;">
            <div class="calc-summary-title">Distribution Summary</div>
            <div id="summary-rows"></div>
        </div>
    </div>
</div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Create Event</button>
            <a href="{{ route('admin.relief.index') }}" class="btn-secondary">Cancel</a>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.select-all').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const municipalityId = this.dataset.municipality;
        document.querySelectorAll('.brgy-' + municipalityId).forEach(brgy => {
            brgy.checked = this.checked;
        });
    });
});

function calculateDistribution() {
    const households = parseInt(document.getElementById('total-households').value) || 0;
    const checkboxes = document.querySelectorAll('input[name="distribute_items[]"]:checked');
    const summaryDiv = document.getElementById('calc-summary');
    const summaryRows = document.getElementById('summary-rows');

    document.querySelectorAll('.calc-result').forEach(el => {
        el.textContent = '—';
        el.classList.remove('warning');
    });

    if (households <= 0 || checkboxes.length === 0) {
        summaryDiv.style.display = 'none';
        return;
    }

    let summaryHTML = '';

    checkboxes.forEach(checkbox => {
        const itemId       = checkbox.value;
        const stock        = parseInt(checkbox.dataset.quantity) || 0;
        const name         = checkbox.dataset.name;
        const unit         = checkbox.dataset.unit;
        const perHousehold = Math.floor(stock / households);
        const resultEl     = document.getElementById('result-' + itemId);

        if (perHousehold <= 0) {
            resultEl.textContent = 'Insufficient stock';
            resultEl.classList.add('warning');
            summaryHTML += `<div class="summary-row">
                <span class="summary-item">${name}</span>
                <span style="color:#a32d2d;">Not enough stock (${stock} ${unit} for ${households} households)</span>
            </div>`;
        } else {
            const totalUsed = perHousehold * households;
            const remaining = stock - totalUsed;
            resultEl.textContent = `${perHousehold} ${unit}/household`;
            summaryHTML += `<div class="summary-row">
                <span class="summary-item">${name}</span>
                <span>${perHousehold} ${unit} × ${households} = ${totalUsed} used, ${remaining} remaining</span>
            </div>`;
        }
    });

    summaryRows.innerHTML = summaryHTML;
    summaryDiv.style.display = 'block';
}
</script>
@endpush