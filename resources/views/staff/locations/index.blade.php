@extends('staff.layouts.app')
@section('title', 'Location Requests')

@section('content')
<div class="dash-header">
    <div class="dash-header-content">
        <h1>Location Requests</h1>
        <p style="color:#666;font-size:0.9rem;margin-top:0.5rem;">Submit requests for new municipalities and barangays to be added to the system</p>
    </div>
    <div class="dash-header-actions">
        <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
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
        <form method="POST" action="{{ route('staff.location-requests.store') }}">
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
        <form method="POST" action="{{ route('staff.location-requests.store') }}">
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


@push('styles')
<style>
.dash-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.5rem;
    background: white;
    padding: 1.25rem 1.5rem;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.dash-header-content {
    flex: 1;
}

.dash-header-actions {
    flex-shrink: 0;
}

.dash-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c2c2a;
    margin: 0 0 0.25rem;
}

.btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    background: #6b7280;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background 0.2s;
    border: none;
    cursor: pointer;
}

.btn-secondary:hover {
    background: #4b5563;
}

@media (max-width: 768px) {
    .dash-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .dash-header-actions {
        width: 100%;
    }
    
    .btn-secondary {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@endsection