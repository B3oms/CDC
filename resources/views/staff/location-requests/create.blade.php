@extends('admin.layouts.app')
@section('title', 'Create Location Request')
@section('breadcrumb', '<i class="fas fa-map-marker-alt"></i> Location Management / New Request')

@section('content')

<div class="page-header">
    <div class="page-title">
        <h1>Create Location Request</h1>
        <p class="page-description">Submit a new municipality or barangay for admin approval</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('staff.location-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Requests
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-card">
    <form method="POST" action="{{ route('staff.location-requests.store') }}">
        @csrf
        
        <div class="form-section">
            <div class="section-header">
                <h3><i class="fas fa-info-circle"></i> Request Details</h3>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="type">Request Type *</label>
                    <select id="type" name="type" required onchange="toggleMunicipalityField()">
                        <option value="">Select Type</option>
                        <option value="municipality" {{ old('type') == 'municipality' ? 'selected' : '' }}>
                            Municipality
                        </option>
                        <option value="barangay" {{ old('type') == 'barangay' ? 'selected' : '' }}>
                            Barangay
                        </option>
                    </select>
                    <small class="form-help">Choose whether you're requesting a new municipality or barangay</small>
                </div>
                
                <div class="form-group">
                    <label for="name">Location Name *</label>
                    <input type="text" id="name" name="name"
                        value="{{ old('name') }}"
                        placeholder="e.g. San Rafael" required>
                    <small class="form-help">Enter the official name of the location</small>
                </div>
            </div>
            
            <div class="form-row" id="provinceRow" style="display: none;">
                <div class="form-group">
                    <label for="region">Region *</label>
                    <select id="region" name="region">
                        <option value="">Select Region</option>
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
                    <small class="form-help">Required only for municipality requests</small>
                </div>
            </div>
            
            <div class="form-row" id="municipalityRow" style="display: none;">
                <div class="form-group">
                    <label for="municipality_id">Parent Municipality *</label>
                    <select id="municipality_id" name="municipality_id">
                        <option value="">Select Municipality</option>
                        @foreach($municipalities as $municipality)
                            <option value="{{ $municipality->id }}"
                                {{ old('municipality_id') == $municipality->id ? 'selected' : '' }}>
                                {{ $municipality->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-help">Required only for barangay requests</small>
                </div>
                
                <div class="form-group">
                    <div class="form-help">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Barangay requests must be assigned to an existing approved municipality.
                    </div>
                </div>
            </div>
            
            <div class="form-group full-width">
                <label for="remarks">Remarks (Optional)</label>
                <textarea id="remarks" name="remarks" rows="4"
                    placeholder="Additional information about this location request..."
                    style="resize: vertical;">{{ old('remarks') }}</textarea>
                <small class="form-help">Provide any additional context or special requirements</small>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-paper-plane"></i> Submit Request
            </button>
            <a href="{{ route('staff.location-requests.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleMunicipalityField() {
    const type = document.getElementById('type').value;
    const municipalityRow = document.getElementById('municipalityRow');
    const provinceRow = document.getElementById('provinceRow');
    const municipalitySelect = document.getElementById('municipality_id');
    
    console.log('Toggle function called. Type:', type);
    
    if (type === 'barangay') {
        municipalityRow.style.display = 'flex';
        provinceRow.style.display = 'none';
        municipalitySelect.required = true;
        document.getElementById('region').required = false;
        document.getElementById('region').value = '';
        console.log('Showing barangay field, hiding region field');
    } else if (type === 'municipality') {
        municipalityRow.style.display = 'none';
        provinceRow.style.display = 'flex';
        municipalitySelect.required = false;
        municipalitySelect.value = '';
        document.getElementById('region').required = true;
        console.log('Showing region field, hiding barangay field');
    } else {
        municipalityRow.style.display = 'none';
        provinceRow.style.display = 'none';
        municipalitySelect.required = false;
        municipalitySelect.value = '';
        document.getElementById('region').required = false;
        document.getElementById('region').value = '';
        console.log('Hiding both fields');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleMunicipalityField();
});
</script>
@endpush
