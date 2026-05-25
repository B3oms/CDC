@extends('staff.layouts.app')
@section('title', 'Add Beneficiary')

@push('styles')
<style>
.alert-error {
    background: #fef2f2;
    border: 1px solid #fca5a5;
    color: #991b1b;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-error i {
    font-size: 1rem;
}

/* Badge Styles */
.info-badge {
    background: #3b82f6;
    color: white;
    font-size: 0.7rem;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 500;
    margin-left: 0.5rem;
}

.required {
    color: #dc3545;
    font-weight: 600;
}

/* Family Background Styles */
.family-member-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
}

.member-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.member-header h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
    margin: 0;
}

.btn-add-child {
    background: #28a745;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: background 0.2s ease;
}

.btn-add-child:hover {
    background: #218838;
}

.btn-remove-child {
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    cursor: pointer;
    transition: background 0.2s ease;
}

.btn-remove-child:hover {
    background: #c82333;
}

.child-card {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.child-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.child-number {
    font-weight: 600;
    color: #007bff;
    margin: 0;
}
</style>
@endpush

@section('content')
<div class="dash-header">
    <h1>Beneficiary Interview Form</h1>
    <x-back-button href="{{ route('staff.beneficiaries.index') }}" label="Back" />
</div>

@if(isset($prefill))
<div class="alert-calamity" style="margin-bottom:1rem;">
    <strong>Pre-filled from Recommendation:</strong>
    {{ $prefill->first_name }} {{ $prefill->last_name }} — {{ $prefill->barangay->name }}
</div>
<input type="hidden" name="recommended_id" value="{{ $recommended_id ?? $prefill->id }}">
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
                        value="{{ old('first_name', $prefill->first_name ?? '') }}" placeholder="Enter first name" required
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                        onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
                </div>
                <div class="form-group">
                    <label>Middle Name (Optional)</label>
                    <input type="text" name="middle_name"
                        value="{{ old('middle_name', $prefill->middle_name ?? '') }}" placeholder="Enter middle name"
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                        onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name"
                        value="{{ old('last_name', $prefill->last_name ?? '') }}" placeholder="Enter last name" required
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                        onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
                </div>
                <div class="form-group">
                    <label>Suffix (Optional)</label>
                    <select name="suffix">
                        <option value="">-- None --</option>
                        <option value="Jr." {{ old('suffix', $prefill->suffix ?? '') == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                        <option value="Sr." {{ old('suffix', $prefill->suffix ?? '') == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                        <option value="I" {{ old('suffix', $prefill->suffix ?? '') == 'I' ? 'selected' : '' }}>I</option>
                        <option value="II" {{ old('suffix', $prefill->suffix ?? '') == 'II' ? 'selected' : '' }}>II</option>
                        <option value="III" {{ old('suffix', $prefill->suffix ?? '') == 'III' ? 'selected' : '' }}>III</option>
                        <option value="IV" {{ old('suffix', $prefill->suffix ?? '') == 'IV' ? 'selected' : '' }}>IV</option>
                        <option value="V" {{ old('suffix', $prefill->suffix ?? '') == 'V' ? 'selected' : '' }}>V</option>
                    </select>
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
                    <label>Age</label>
                    <input type="number" name="age" value="{{ old('age') }}" required
                           min="1" max="120" placeholder="Enter age">
                </div>
                <div class="form-group">
                    <label>Birthdate</label>
                    <input type="date" name="birthdate" value="{{ old('birthdate') }}" placeholder="Select birthdate" required>
                </div>
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number"
                        value="{{ old('contact_number', $prefill->contact_number ?? '') }}"
                        placeholder="09XXXXXXXXX" maxlength="11" pattern="[0-9]{11}"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                        onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                </div>
                <div class="form-group">
                    <label>Region</label>
                    <select name="region" id="region" required onchange="updateMunicipalities()">
                        <option value="">-- Select Region --</option>
                        <option value="Region I" {{ old('region') == 'Region I' ? 'selected' : '' }}>Region I (Ilocos Region)</option>
                        <option value="Region II" {{ old('region') == 'Region II' ? 'selected' : '' }}>Region II (Cagayan Valley)</option>
                        <option value="Region III" {{ old('region') == 'Region III' ? 'selected' : '' }}>Region III (Central Luzon)</option>
                        <option value="Region IV-A" {{ old('region') == 'Region IV-A' ? 'selected' : '' }}>Region IV-A (CALABARZON)</option>
                        <option value="Region IV-B" {{ old('region') == 'Region IV-B' ? 'selected' : '' }}>Region IV-B (MIMAROPA)</option>
                        <option value="Region V" {{ old('region') == 'Region V' ? 'selected' : '' }}>Region V (Bicol Region)</option>
                        <option value="Region VI" {{ old('region') == 'Region VI' ? 'selected' : '' }}>Region VI (Western Visayas)</option>
                        <option value="Region VII" {{ old('region') == 'Region VII' ? 'selected' : '' }}>Region VII (Central Visayas)</option>
                        <option value="Region VIII" {{ old('region') == 'Region VIII' ? 'selected' : '' }}>Region VIII (Eastern Visayas)</option>
                        <option value="Region IX" {{ old('region') == 'Region IX' ? 'selected' : '' }}>Region IX (Zamboanga Peninsula)</option>
                        <option value="Region X" {{ old('region') == 'Region X' ? 'selected' : '' }}>Region X (Northern Mindanao)</option>
                        <option value="Region XI" {{ old('region') == 'Region XI' ? 'selected' : '' }}>Region XI (Davao Region)</option>
                        <option value="Region XII" {{ old('region') == 'Region XII' ? 'selected' : '' }}>Region XII (SOCCSKSARGEN)</option>
                        <option value="Region XIII" {{ old('region') == 'Region XIII' ? 'selected' : '' }}>Region XIII (Caraga)</option>
                        <option value="NCR" {{ old('region') == 'NCR' ? 'selected' : '' }}>NCR (National Capital Region)</option>
                        <option value="CAR" {{ old('region') == 'CAR' ? 'selected' : '' }}>CAR (Cordillera Administrative Region)</option>
                        <option value="BARMM" {{ old('region') == 'BARMM' ? 'selected' : '' }}>BARMM (Bangsamoro)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Municipality</label>
                    <select name="municipality_id" id="municipality" required onchange="updateBarangays()" disabled>
                        <option value="">-- Select Municipality --</option>
                        @foreach($municipalities as $m)
                        <option value="{{ $m->id }}" data-region="{{ $m->province }}"
                            {{ old('municipality_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Barangay</label>
                    <select name="barangay_id" id="barangay" required disabled>
                        <option value="">-- Select Barangay --</option>
                        @foreach($barangays as $b)
                        <option value="{{ $b->id }}" data-municipality="{{ $b->municipality_id }}"
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
                <div class="form-group">
                    <label>
                        Indigenous Status
                    </label>
                    <select name="is_indigenous">
                        <option value="">-- Select --</option>
                        <option value="0" {{ old('is_indigenous') == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('is_indigenous') == '1' ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Section 2: Family Background --}}
        <div class="interview-section">
            <div class="interview-section-title">Family Background</div>
            
            {{-- Mother Information --}}
            <div class="family-member-card">
                <div class="member-header">
                    <h4>Mother Information</h4>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Mother's Name</label>
                        <input type="text" name="mother_name" value="{{ old('mother_name') }}" 
                               placeholder="Enter mother's full name">
                    </div>
                    <div class="form-group">
                        <label>Mother's Age</label>
                        <input type="number" name="mother_age" value="{{ old('mother_age') }}" 
                               min="1" max="120" placeholder="Enter age">
                    </div>
                    <div class="form-group">
                        <label>Mother's Sex</label>
                        <select name="mother_sex">
                            <option value="">-- Select --</option>
                            <option value="female" {{ old('mother_sex') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="male" {{ old('mother_sex') == 'male' ? 'selected' : '' }}>Male</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Mother's Birthdate</label>
                        <input type="date" name="mother_birthdate" value="{{ old('mother_birthdate') }}" 
                               max="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
            </div>

            {{-- Father Information --}}
            <div class="family-member-card">
                <div class="member-header">
                    <h4>Father Information</h4>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Father's Name</label>
                        <input type="text" name="father_name" value="{{ old('father_name') }}" 
                               placeholder="Enter father's full name">
                    </div>
                    <div class="form-group">
                        <label>Father's Age</label>
                        <input type="number" name="father_age" value="{{ old('father_age') }}" 
                               min="1" max="120" placeholder="Enter age">
                    </div>
                    <div class="form-group">
                        <label>Father's Sex</label>
                        <select name="father_sex">
                            <option value="">-- Select --</option>
                            <option value="male" {{ old('father_sex') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('father_sex') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Father's Birthdate</label>
                        <input type="date" name="father_birthdate" value="{{ old('father_birthdate') }}" 
                               max="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
            </div>

            {{-- Spouse Information --}}
            <div class="family-member-card">
                <div class="member-header">
                    <h4>Spouse Information (if applicable)</h4>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Spouse's Name</label>
                        <input type="text" name="spouse_name" value="{{ old('spouse_name') }}" 
                               placeholder="Enter spouse's full name">
                    </div>
                    <div class="form-group">
                        <label>Spouse's Age</label>
                        <input type="number" name="spouse_age" value="{{ old('spouse_age') }}" 
                               min="1" max="120" placeholder="Enter age">
                    </div>
                    <div class="form-group">
                        <label>Spouse's Sex</label>
                        <select name="spouse_sex">
                            <option value="">-- Select --</option>
                            <option value="male" {{ old('spouse_sex') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('spouse_sex') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Spouse's Birthdate</label>
                        <input type="date" name="spouse_birthdate" value="{{ old('spouse_birthdate') }}" 
                               max="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>Spouse's Occupation</label>
                        <input type="text" name="spouse_occupation" value="{{ old('spouse_occupation') }}" 
                               placeholder="Enter spouse's occupation">
                    </div>
                </div>
            </div>

            {{-- Children Information --}}
            <div class="family-member-card">
                <div class="member-header">
                    <h4>Children Information</h4>
                    <button type="button" class="btn-add-child" onclick="addChild()">
                        <i class="fas fa-plus"></i> Add Child
                    </button>
                </div>
                <div id="children-container">
                    <!-- Children will be dynamically added here -->
                </div>
            </div>
        </div>

        {{-- Section 3: Verification Criteria --}}
        <div class="interview-section">
            <div class="interview-section-title">Verification Criteria</div>
            <p class="hint" style="margin-bottom:1rem;">
                Beneficiary must meet at least <strong>3 of 6 criteria</strong> to be verified.
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
                <div class="form-group">
                    <label>
                        4Ps Member
                        <span class="criteria-badge" id="crit-4ps">Criteria 5: 4Ps member</span>
                    </label>
                    <select name="is_4ps_member" onchange="updateCriteria()">
                        <option value="">-- Select --</option>
                        <option value="0" {{ old('is_4ps_member') == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('is_4ps_member') == '1' ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>
                        Person with Disability (PWD)
                        <span class="criteria-badge" id="crit-pwd">Criteria 6: PWD present</span>
                    </label>
                    <select name="is_pwd" id="pwd-select" onchange="togglePwdType(); updateCriteria()">
                        <option value="">-- Select --</option>
                        <option value="0" {{ old('is_pwd') == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('is_pwd') == '1' ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
            </div>

            {{-- PWD Type Section --}}
            <div class="form-grid" style="margin-top:1.5rem;">
                <div class="form-group" id="pwd-type-group" style="display:none;">
                    <label>
                        Type of Disability
                        <span class="required">*</span>
                    </label>
                    <select name="pwd_type" id="pwd-type-select">
                        <option value="">-- Select Type --</option>
                        <option value="visual" {{ old('pwd_type') == 'visual' ? 'selected' : '' }}>Visual Impairment</option>
                        <option value="hearing" {{ old('pwd_type') == 'hearing' ? 'selected' : '' }}>Hearing Impairment</option>
                        <option value="mobility" {{ old('pwd_type') == 'mobility' ? 'selected' : '' }}>Mobility Impairment</option>
                        <option value="cognitive" {{ old('pwd_type') == 'cognitive' ? 'selected' : '' }}>Cognitive Disability</option>
                        <option value="psychosocial" {{ old('pwd_type') == 'psychosocial' ? 'selected' : '' }}>Psychosocial Disability</option>
                        <option value="multiple" {{ old('pwd_type') == 'multiple' ? 'selected' : '' }}>Multiple Disabilities</option>
                        <option value="other" {{ old('pwd_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            {{-- Live Criteria Result --}}
            <div class="criteria-result" id="criteria-result">
                <div class="criteria-count" id="criteria-count">0/5 criteria met</div>
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

        {{-- Duplicate Error Display --}}
        @if($errors->has('duplicate'))
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first('duplicate') }}
            </div>
        @endif

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
    const fourPs   = document.querySelector('[name="is_4ps_member"]').value === '1';
    const pwd      = document.querySelector('[name="is_pwd"]').value === '1';

    const c1 = income <= 10000;
    const c2 = family >= 4;
    const c3 = children >= 2;
    const c4 = senior;
    const c5 = fourPs;
    const c6 = pwd;

    let count = 0;
    if (c1) count++;
    if (c2) count++;
    if (c3) count++;
    if (c4) count++;
    if (c5) count++;
    if (c6) count++;

    // Update badges
    document.getElementById('crit-income').className   = 'criteria-badge ' + (c1 ? 'met' : '');
    document.getElementById('crit-family').className   = 'criteria-badge ' + (c2 ? 'met' : '');
    document.getElementById('crit-children').className = 'criteria-badge ' + (c3 ? 'met' : '');
    document.getElementById('crit-senior').className   = 'criteria-badge ' + (c4 ? 'met' : '');
    document.getElementById('crit-4ps').className       = 'criteria-badge ' + (c5 ? 'met' : '');
    document.getElementById('crit-pwd').className        = 'criteria-badge ' + (c6 ? 'met' : '');

    const countEl   = document.getElementById('criteria-count');
    const verdictEl = document.getElementById('criteria-verdict');
    const resultEl  = document.getElementById('criteria-result');

    countEl.textContent = count + '/6 criteria met';

    if (count >= 4) {
        verdictEl.textContent = '✓ Will be verified — High Vulnerability';
        resultEl.className    = 'criteria-result high';
    } else if (count === 3) {
        verdictEl.textContent = '✓ Will be verified — Medium Vulnerability';
        resultEl.className    = 'criteria-result medium';
    } else {
        verdictEl.textContent = '✗ Will NOT be verified (' + count + '/6 — needs at least 3)';
        resultEl.className    = 'criteria-result low';
    }
}

updateCriteria();

// Municipality data for dynamic loading
const municipalityData = @json($municipalities->load('barangays'));

function updateMunicipalities() {
    const regionSelect = document.getElementById('region');
    const municipalitySelect = document.getElementById('municipality');
    const barangaySelect = document.getElementById('barangay');
    const selectedRegion = regionSelect.value;
    
    // Clear current selections
    municipalitySelect.innerHTML = '<option value="">-- Select Municipality --</option>';
    barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
    
    if (selectedRegion) {
        // Filter municipalities by region (using province as region identifier)
        const filteredMunicipalities = municipalityData.filter(m => {
            // Map provinces to regions (you may need to adjust this mapping based on your data)
            return getRegionFromProvince(m.province) === selectedRegion;
        });
        
        filteredMunicipalities.forEach(municipality => {
            const option = document.createElement('option');
            option.value = municipality.id;
            option.textContent = municipality.name;
            option.setAttribute('data-region', selectedRegion);
            municipalitySelect.appendChild(option);
        });
    }
    
    // Enable/disable selects based on selection
    municipalitySelect.disabled = !selectedRegion;
    barangaySelect.disabled = true;
}

function updateBarangays() {
    const municipalitySelect = document.getElementById('municipality');
    const barangaySelect = document.getElementById('barangay');
    const selectedMunicipalityId = municipalitySelect.value;
    
    // Clear current barangay options
    barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
    
    if (selectedMunicipalityId) {
        const municipality = municipalityData.find(m => m.id == selectedMunicipalityId);
        if (municipality && municipality.barangays) {
            municipality.barangays.forEach(barangay => {
                const option = document.createElement('option');
                option.value = barangay.id;
                option.textContent = barangay.name;
                barangaySelect.appendChild(option);
            });
        }
    }
    
    // Enable/disable barangay select based on municipality selection
    barangaySelect.disabled = !selectedMunicipalityId;
}

function getRegionFromProvince(province) {
    // Map provinces to regions (adjust based on your actual data)
    const regionMapping = {
        'Ilocos Norte': 'Region I',
        'Ilocos Sur': 'Region I',
        'La Union': 'Region I',
        'Pangasinan': 'Region I',
        'Batanes': 'Region II',
        'Cagayan': 'Region II',
        'Isabela': 'Region II',
        'Nueva Vizcaya': 'Region II',
        'Quirino': 'Region II',
        'Aurora': 'Region III',
        'Bataan': 'Region III',
        'Bulacan': 'Region III',
        'Nueva Ecija': 'Region III',
        'Pampanga': 'Region III',
        'Tarlac': 'Region III',
        'Zambales': 'Region III',
        'Batangas': 'Region IV-A',
        'Cavite': 'Region IV-A',
        'Laguna': 'Region IV-A',
        'Quezon': 'Region IV-A',
        'Rizal': 'Region IV-A',
        'Marinduque': 'Region IV-B',
        'Occidental Mindoro': 'Region IV-B',
        'Oriental Mindoro': 'Region IV-B',
        'Palawan': 'Region IV-B',
        'Romblon': 'Region IV-B',
        'Albay': 'Region V',
        'Camarines Norte': 'Region V',
        'Camarines Sur': 'Region V',
        'Catanduanes': 'Region V',
        'Masbate': 'Region V',
        'Sorsogon': 'Region V',
        'Aklan': 'Region VI',
        'Antique': 'Region VI',
        'Capiz': 'Region VI',
        'Guimaras': 'Region VI',
        'Iloilo': 'Region VI',
        'Negros Occidental': 'Region VI',
        'Bohol': 'Region VII',
        'Cebu': 'Region VII',
        'Negros Oriental': 'Region VII',
        'Siquijor': 'Region VII',
        'Biliran': 'Region VIII',
        'Eastern Samar': 'Region VIII',
        'Leyte': 'Region VIII',
        'Northern Samar': 'Region VIII',
        'Samar': 'Region VIII',
        'Southern Leyte': 'Region VIII',
        'Zamboanga del Norte': 'Region IX',
        'Zamboanga del Sur': 'Region IX',
        'Zamboanga Sibugay': 'Region IX',
        'Bukidnon': 'Region X',
        'Camiguin': 'Region X',
        'Lanao del Norte': 'Region X',
        'Misamis Occidental': 'Region X',
        'Misamis Oriental': 'Region X',
        'Davao de Oro': 'Region XI',
        'Davao del Norte': 'Region XI',
        'Davao del Sur': 'Region XI',
        'Davao Occidental': 'Region XI',
        'Davao Oriental': 'Region XI',
        'Compostela Valley': 'Region XI',
        'Cotabato': 'Region XII',
        'Lanao del Sur': 'Region XII',
        'Maguindanao': 'Region XII',
        'Sarangani': 'Region XII',
        'Sultan Kudarat': 'Region XII',
        'Agusan del Norte': 'Region XIII',
        'Agusan del Sur': 'Region XIII',
        'Dinagat Islands': 'Region XIII',
        'Surigao del Norte': 'Region XIII',
        'Surigao del Sur': 'Region XIII',
        'Abra': 'CAR',
        'Apayao': 'CAR',
        'Benguet': 'CAR',
        'Ifugao': 'CAR',
        'Kalinga': 'CAR',
        'Mountain Province': 'CAR'
    };
    
    return regionMapping[province] || '';
}

// Toggle PWD type field based on PWD selection
function togglePwdType() {
    const pwdSelect = document.getElementById('pwd-select');
    const pwdTypeGroup = document.getElementById('pwd-type-group');
    const pwdTypeSelect = document.getElementById('pwd-type-select');
    
    if (pwdSelect.value === '1') {
        pwdTypeGroup.style.display = 'block';
        pwdTypeSelect.required = true;
    } else {
        pwdTypeGroup.style.display = 'none';
        pwdTypeSelect.required = false;
        pwdTypeSelect.value = '';
    }
}

// Children management
let childCount = 0;

function addChild() {
    childCount++;
    const container = document.getElementById('children-container');
    
    const childCard = document.createElement('div');
    childCard.className = 'child-card';
    childCard.innerHTML = `
        <div class="child-header">
            <h5 class="child-number">Child ${childCount}</h5>
            <button type="button" class="btn-remove-child" onclick="removeChild(this)">
                <i class="fas fa-times"></i> Remove
            </button>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label>Child's Name</label>
                <input type="text" name="children[${childCount}][name]" 
                       placeholder="Enter child's full name">
            </div>
            <div class="form-group">
                <label>Child's Age</label>
                <input type="number" name="children[${childCount}][age]" 
                       min="0" max="120" placeholder="Enter age">
            </div>
            <div class="form-group">
                <label>Child's Sex</label>
                <select name="children[${childCount}][sex]">
                    <option value="">-- Select --</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label>Child's Birthdate</label>
                <input type="date" name="children[${childCount}][birthdate]" 
                       max="{{ now()->format('Y-m-d') }}">
            </div>
        </div>
    `;
    
    container.appendChild(childCard);
}

function removeChild(button) {
    const childCard = button.closest('.child-card');
    childCard.remove();
    updateChildNumbers();
}

function updateChildNumbers() {
    const container = document.getElementById('children-container');
    const children = container.querySelectorAll('.child-card');
    
    children.forEach((child, index) => {
        const title = child.querySelector('.child-number');
        if (title) {
            title.textContent = `Child ${index + 1}`;
        }
    });
    
    childCount = children.length;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateMunicipalities();
    togglePwdType(); // Initialize PWD type field state
    addChild(); // Add one child by default
});
</script>
@endpush