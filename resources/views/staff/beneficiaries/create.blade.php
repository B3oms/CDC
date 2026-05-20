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
                        value="{{ old('first_name', $prefill->first_name ?? '') }}" required
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                        onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
                </div>
                <div class="form-group">
                    <label>Middle Name (Optional)</label>
                    <input type="text" name="middle_name"
                        value="{{ old('middle_name', $prefill->middle_name ?? '') }}"
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                        onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name"
                        value="{{ old('last_name', $prefill->last_name ?? '') }}" required
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                        onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
                </div>
                <div class="form-group">
                    <label>Suffix (Optional)</label>
                    <input type="text" name="suffix"
                        value="{{ old('suffix', $prefill->suffix ?? '') }}" placeholder="e.g. Jr., Sr., III">
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
                        placeholder="09XXXXXXXXX" maxlength="11" pattern="[0-9]{11}"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                        onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                    <small style="color: #666; font-size: 12px;">Must be exactly 11 digits (numbers only)</small>
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
            </div>
        </div>

        {{-- Section 2: Verification Criteria --}}
        <div class="interview-section">
            <div class="interview-section-title">Verification Criteria</div>
            <p class="hint" style="margin-bottom:1rem;">
                Beneficiary must meet at least <strong>3 of 5 criteria</strong> to be verified.
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

    const c1 = income <= 10000;
    const c2 = family >= 4;
    const c3 = children >= 2;
    const c4 = senior;
    const c5 = fourPs;

    let count = 0;
    if (c1) count++;
    if (c2) count++;
    if (c3) count++;
    if (c4) count++;
    if (c5) count++;

    // Update badges
    document.getElementById('crit-income').className   = 'criteria-badge ' + (c1 ? 'met' : '');
    document.getElementById('crit-family').className   = 'criteria-badge ' + (c2 ? 'met' : '');
    document.getElementById('crit-children').className = 'criteria-badge ' + (c3 ? 'met' : '');
    document.getElementById('crit-senior').className   = 'criteria-badge ' + (c4 ? 'met' : '');
    document.getElementById('crit-4ps').className       = 'criteria-badge ' + (c5 ? 'met' : '');

    const countEl   = document.getElementById('criteria-count');
    const verdictEl = document.getElementById('criteria-verdict');
    const resultEl  = document.getElementById('criteria-result');

    countEl.textContent = count + '/5 criteria met';

    if (count >= 4) {
        verdictEl.textContent = '✓ Will be verified — High Vulnerability';
        resultEl.className    = 'criteria-result high';
    } else if (count === 3) {
        verdictEl.textContent = '✓ Will be verified — Medium Vulnerability';
        resultEl.className    = 'criteria-result medium';
    } else {
        verdictEl.textContent = '✗ Will NOT be verified (' + count + '/5 — needs at least 3)';
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateMunicipalities();
});
</script>
@endpush