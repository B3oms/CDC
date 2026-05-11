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
    const municipalitySelect = document.getElementById('municipality_id');
    
    if (type === 'barangay') {
        municipalityRow.style.display = 'flex';
        municipalitySelect.required = true;
    } else {
        municipalityRow.style.display = 'none';
        municipalitySelect.required = false;
        municipalitySelect.value = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleMunicipalityField();
});
</script>
@endpush
