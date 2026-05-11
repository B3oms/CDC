@extends('staff.layouts.app')
@section('title', 'Add Beneficiary')

@section('content')
<div class="dash-header">
    <h1>Beneficiary Interview Form</h1>
    <a href="{{ route('staff.beneficiaries.index') }}" class="btn-back">← Back</a>
</div>

@if(isset($prefill))
<div class="alert-calamity" style="margin-bottom:1rem;">
    <strong>Pre-filled from Recommendation:</strong>
    {{ $prefill->first_name }} {{ $prefill->last_name }} — {{ $prefill->barangay->name }}
</div>
@endif

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

    <form method="POST" action="{{ route('staff.beneficiaries.store') }}">
        @csrf

        {{-- Section 1: Personal Info --}}
        <div class="interview-section">
            <div class="interview-section-title">Personal Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name"
                        value="{{ old('first_name', $prefill->first_name ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name"
                        value="{{ old('last_name', $prefill->last_name ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="">-- Select --</option>
                        <option value="Male"   {{ old('gender') == 'Male'   ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other"  {{ old('gender') == 'Other'  ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Birthdate</label>
                    <input type="date" name="birthdate" value="{{ old('birthdate') }}" required>
                </div>
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number"
                        value="{{ old('contact_number', $prefill->contact_number ?? '') }}"
                        placeholder="09XXXXXXXXX">
                </div>
                <div class="form-group">
                    <label>Barangay</label>
                    <select name="barangay_id" required>
                        <option value="">-- Select Barangay --</option>
                        @foreach($barangays as $b)
                        <option value="{{ $b->id }}"
                            {{ old('barangay_id', $prefill->barangay_id ?? '') == $b->id ? 'selected' : '' }}>
                            {{ $b->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group full-width">
                    <label>Address</label>
                    <textarea name="address" rows="2"
                        placeholder="Full address">{{ old('address', $prefill->address ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 2: Verification Criteria --}}
        <div class="interview-section">
            <div class="interview-section-title">Verification Criteria</div>
            <p class="hint" style="margin-bottom:1rem;">
                Beneficiary must meet at least <strong>2 of 4 criteria</strong> to be verified.
            </p>
            <div class="form-grid">
                <div class="form-group">
                    <label>
                        Monthly Income
                        <span class="criteria-badge" id="crit-income">Criteria 1: ≤ ₱10,000</span>
                    </label>
                    <input type="number" name="monthly_income"
                        value="{{ old('monthly_income') }}"
                        placeholder="e.g. 8000" min="0" step="0.01"
                        required oninput="updateCriteria()">
                </div>
                <div class="form-group">
                    <label>
                        Family Size (members sharing house)
                        <span class="criteria-badge" id="crit-family">Criteria 2: ≥ 4 members</span>
                    </label>
                    <input type="number" name="family_size"
                        value="{{ old('family_size') }}"
                        placeholder="e.g. 6" min="1"
                        required oninput="updateCriteria()">
                </div>
                <div class="form-group">
                    <label>
                        Number of Children (≤ 12 years old)
                        <span class="criteria-badge" id="crit-children">Criteria 3: ≥ 2 children</span>
                    </label>
                    <input type="number" name="children_count"
                        value="{{ old('children_count', 0) }}"
                        placeholder="e.g. 3" min="0"
                        required oninput="updateCriteria()">
                </div>
                <div class="form-group">
                    <label>
                        Has Senior Citizen in Household?
                        <span class="criteria-badge" id="crit-senior">Criteria 4: Senior present</span>
                    </label>
                    <select name="has_senior" onchange="updateCriteria()">
                        <option value="0" {{ old('has_senior') == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('has_senior') == '1' ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
            </div>

            {{-- Live Criteria Result --}}
            <div class="criteria-result" id="criteria-result">
                <div class="criteria-count" id="criteria-count">0/4 criteria met</div>
                <div class="criteria-verdict" id="criteria-verdict">Fill in the form above</div>
            </div>
        </div>

        {{-- Section 3: Notes --}}
        <div class="interview-section">
            <div class="interview-section-title">Interview Notes</div>
            <div class="form-group">
                <label>Additional Notes</label>
                <textarea name="interview_notes" rows="3"
                    placeholder="Any additional observations from the interview...">{{ old('interview_notes') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Submit Interview</button>
            <a href="{{ route('staff.beneficiaries.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function updateCriteria() {
    const income   = parseFloat(document.querySelector('[name="monthly_income"]').value) || 0;
    const family   = parseInt(document.querySelector('[name="family_size"]').value) || 0;
    const children = parseInt(document.querySelector('[name="children_count"]').value) || 0;
    const senior   = document.querySelector('[name="has_senior"]').value === '1';

    let count = 0;
    const c1 = income > 0 && income <= 10000;
    const c2 = family >= 4;
    const c3 = children >= 2;
    const c4 = senior;

    if (c1) count++;
    if (c2) count++;
    if (c3) count++;
    if (c4) count++;

    // Update badges
    document.getElementById('crit-income').className   = 'criteria-badge ' + (c1 ? 'met' : '');
    document.getElementById('crit-family').className   = 'criteria-badge ' + (c2 ? 'met' : '');
    document.getElementById('crit-children').className = 'criteria-badge ' + (c3 ? 'met' : '');
    document.getElementById('crit-senior').className   = 'criteria-badge ' + (c4 ? 'met' : '');

    const countEl   = document.getElementById('criteria-count');
    const verdictEl = document.getElementById('criteria-verdict');
    const resultEl  = document.getElementById('criteria-result');

    countEl.textContent = count + '/4 criteria met';

    if (count >= 3) {
        verdictEl.textContent = '✓ Will be verified — High Vulnerability';
        resultEl.className    = 'criteria-result high';
    } else if (count === 2) {
        verdictEl.textContent = '✓ Will be verified — Medium Vulnerability';
        resultEl.className    = 'criteria-result medium';
    } else {
        verdictEl.textContent = '✗ Will NOT be verified (' + count + '/4 — needs at least 2)';
        resultEl.className    = 'criteria-result low';
    }
}
updateCriteria();
</script>
@endpush