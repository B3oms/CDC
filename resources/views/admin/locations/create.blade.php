@extends('admin.layouts.app')
@section('title', 'Location Requests')

@section('content')
<div class="dash-header">
    <div class="dash-header-left">
        <h1>Location Requests</h1>
        <p style="color:#666;font-size:0.9rem;margin-top:0.5rem;">Submit requests for new municipalities and barangays to be added to the system</p>
    </div>
    <div class="dash-header-right">
        <a href="{{ route('admin.location-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Requests
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert-error">{{ session('error') }}</div>
@endif

<div class="relief-section">
    <div class="relief-section-title">Request New Municipality</div>
    <div class="form-card">
        <form method="POST" action="{{ route('admin.location-requests.store') }}">
            @csrf
            <input type="hidden" name="type" value="municipality">
            
            <div class="form-section">
                <div class="section-header">
                    <h3><i class="fas fa-city"></i> Municipality Details</h3>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="municipality_name">Municipality Name *</label>
                        <input type="text" id="municipality_name" name="name"
                            value="{{ old('name') }}"
                            placeholder="e.g. San Rafael" required>
                        <small class="form-help">Enter the official name of the municipality</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="region">Region *</label>
                        <select id="region" name="region" required>
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
                        <small class="form-help">Select the region where the municipality is located</small>
                    </div>
                </div>
                
                <div class="form-group full-width">
                    <label for="municipality_remarks">Remarks (Optional)</label>
                    <textarea id="municipality_remarks" name="remarks" rows="3"
                        placeholder="Additional information about this municipality request..."
                        style="resize: vertical;">{{ old('remarks') }}</textarea>
                    <small class="form-help">Provide any additional context or special requirements</small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-paper-plane"></i> Submit Municipality Request
                </button>
            </div>
        </form>
    </div>
</div>

<div class="relief-section">
    <div class="relief-section-title">Request New Barangay</div>
    <div class="form-card">
        <form method="POST" action="{{ route('admin.location-requests.store') }}">
            @csrf
            <input type="hidden" name="type" value="barangay">
            
            <div class="form-section">
                <div class="section-header">
                    <h3><i class="fas fa-map-marked-alt"></i> Barangay Details</h3>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="barangay_name">Barangay Name *</label>
                        <input type="text" id="barangay_name" name="name"
                            value="{{ old('name') }}"
                            placeholder="e.g. Poblacion" required>
                        <small class="form-help">Enter the official name of the barangay</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="municipality_id">Parent Municipality *</label>
                        <select id="municipality_id" name="municipality_id" required>
                            <option value="">Select Municipality</option>
                            @foreach($municipalities as $municipality)
                                <option value="{{ $municipality->id }}"
                                    {{ old('municipality_id') == $municipality->id ? 'selected' : '' }}>
                                    {{ $municipality->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-help">Select the municipality this barangay belongs to</small>
                    </div>
                </div>
                
                <div class="form-group full-width">
                    <div class="form-help">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Barangay requests must be assigned to an existing approved municipality.
                    </div>
                </div>
                
                <div class="form-group full-width">
                    <label for="barangay_remarks">Remarks (Optional)</label>
                    <textarea id="barangay_remarks" name="remarks" rows="3"
                        placeholder="Additional information about this barangay request..."
                        style="resize: vertical;">{{ old('remarks') }}</textarea>
                    <small class="form-help">Provide any additional context or special requirements</small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-paper-plane"></i> Submit Barangay Request
                </button>
            </div>
        </form>
    </div>
</div>


@endsection

@push('styles')
<style>
/* Header Layout - System Consistent */
.dash-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
}

.dash-header-left {
    flex: 1;
}

.dash-header-right {
    margin-left: 2rem;
}

.dash-header h1 {
    color: #2c2c2a;
    font-family: 'Segoe UI', sans-serif;
    font-size: 1.75rem;
    font-weight: 600;
    margin: 0;
}

.dash-header p {
    color: #888780;
    font-family: 'Segoe UI', sans-serif;
    font-size: 0.9rem;
    margin-top: 0.5rem;
}

/* Alert Styling - System Consistent */
.alert-success {
    background: #f0f9f0;
    color: #3b6d11;
    border: 1px solid #c8e6c8;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-error {
    background: #fef2f2;
    color: #e24b4a;
    border: 1px solid #ffcdd2;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Relief Section Styling - System Consistent */
.relief-section {
    background: #fff;
    border: 1px solid #d3d1c7;
    border-radius: 8px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.relief-section-title {
    background: #1a3d1f;
    color: #fff;
    padding: 1rem 1.5rem;
    font-family: 'Segoe UI', sans-serif;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 8px 8px 0 0;
}

/* Form Card Styling - System Consistent */
.form-card {
    padding: 1.5rem;
}

.form-section {
    margin-bottom: 1.5rem;
}

.section-header h3 {
    color: #2c2c2a;
    font-family: 'Segoe UI', sans-serif;
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-header h3 i {
    color: #1a3d1f;
}

/* Form Elements - System Consistent */
.form-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-group {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group.full-width {
    flex: 1 1 100%;
}

.form-group label {
    color: #2c2c2a;
    font-family: 'Segoe UI', sans-serif;
    font-weight: 500;
    font-size: 0.95rem;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 0.75rem;
    border: 1px solid #dcdcdc;
    border-radius: 6px;
    font-family: 'Segoe UI', sans-serif;
    font-size: 0.95rem;
    color: #2c2c2a;
    background: #fff;
    transition: border-color 0.2s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #1a3d1f;
    box-shadow: 0 0 0 2px rgba(26, 61, 31, 0.2);
}

.form-help {
    color: #888780;
    font-family: 'Segoe UI', sans-serif;
    font-size: 0.75rem;
    line-height: 1.4;
}

.form-help i {
    color: #1a3d1f;
}

/* Form Actions - System Consistent */
.form-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
}

/* Button Styling - System Consistent */
.btn-success {
    background: #3b6d11;
    color: #fff;
    padding: 8px 20px;
    border: none;
    border-radius: 6px;
    font-family: 'Segoe UI', sans-serif;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-success:hover {
    background: #27500a;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(59, 109, 17, 0.2);
}

.btn-secondary {
    background: #888780;
    color: #fff;
    padding: 8px 20px;
    border: none;
    border-radius: 6px;
    font-family: 'Segoe UI', sans-serif;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-secondary:hover {
    background: #6b6a63;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(136, 135, 128, 0.2);
}

/* Responsive Design */
@media (max-width: 768px) {
    .dash-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .dash-header-right {
        margin-left: 0;
    }
    
    .form-row {
        flex-direction: column;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>
@endpush
