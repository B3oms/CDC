@extends('admin.layouts.app')
@section('title', 'Edit Beneficiary')

@push('styles')
<style>
/* Import necessary styles from location-management.css without affecting other pages */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f1efe8;
}

.page-title h1 {
    margin: 0;
    color: #2c2c2a;
    font-size: 1.75rem;
    font-weight: 600;
}

.page-description {
    margin: 0.5rem 0 0;
    color: #888780;
    font-size: 0.95rem;
}

.page-actions {
    display: flex;
    gap: 0.75rem;
}

/* Form Container */
.form-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

/* Form Sections */
.interview-section {
    margin-bottom: 2rem;
}

.interview-section-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e5e7eb;
}

/* Form Grid */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

/* Form Groups */
.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 0.625rem 0.875rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #1a6b2a;
    box-shadow: 0 0 0 3px rgba(26, 107, 42, 0.1);
}

/* Alert Styles */
.alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    padding: 0.75rem 1rem;
    border-radius: 6px;
    margin-bottom: 1rem;
}

.alert-error ul {
    margin: 0;
    padding-left: 1.25rem;
}

.alert-error li {
    margin-bottom: 0.25rem;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    align-items: center;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.btn-submit, .btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn-submit {
    background: #1a6b2a;
    color: white;
}

.btn-submit:hover {
    background: #145522;
    transform: translateY(-1px);
}

.btn-cancel {
    background: #6b7280;
    color: white;
}

.btn-cancel:hover {
    background: #4b5563;
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn-submit, .btn-cancel {
        justify-content: center;
    }
}
</style>
@endpush

@section('content')
<div class="dash-header">
    <h1>Edit Beneficiary</h1>
    <x-back-button href="{{ route('admin.beneficiaries.index') }}" label="Back" />
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

    <form method="POST" action="{{ route('admin.beneficiaries.update', $beneficiary->id) }}">
        @csrf
        @method('PUT')

        {{-- Section 1: Personal Info --}}
        <div class="interview-section">
            <div class="interview-section-title">Personal Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name"
                        value="{{ old('first_name', $beneficiary->first_name) }}" required
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                        onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
                </div>
                <div class="form-group">
                    <label>Middle Name (Optional)</label>
                    <input type="text" name="middle_name"
                        value="{{ old('middle_name', $beneficiary->middle_name) }}"
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                        onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name"
                        value="{{ old('last_name', $beneficiary->last_name) }}" required
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                        onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
                </div>
                <div class="form-group">
                    <label>Suffix (Optional)</label>
                    <input type="text" name="suffix"
                        value="{{ old('suffix', $beneficiary->suffix) }}"
                        placeholder="Jr., Sr., III, etc.">
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender', $beneficiary->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $beneficiary->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $beneficiary->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Birthdate</label>
                    <input type="date" name="birthdate"
                        value="{{ old('birthdate', $beneficiary->birthdate) }}" required>
                </div>
            </div>
        </div>

        {{-- Section 2: Location --}}
        <div class="interview-section">
            <div class="interview-section-title">Location Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Municipality</label>
                    <select name="municipality_id" id="municipality" required>
                        <option value="">Select Municipality</option>
                        @foreach($municipalities as $m)
                            <option value="{{ $m->id }}" 
                                {{ old('municipality_id', $beneficiary->barangay->municipality_id ?? '') == $m->id ? 'selected' : '' }}>
                                {{ $m->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Barangay</label>
                    <select name="barangay_id" id="barangay" required>
                        <option value="">Select Barangay</option>
                        @foreach($barangays as $b)
                            <option value="{{ $b->id }}" 
                                {{ old('barangay_id', $beneficiary->barangay_id) == $b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address"
                        value="{{ old('address', $beneficiary->address) }}"
                        placeholder="House number, street, etc.">
                </div>
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number"
                        value="{{ old('contact_number', $beneficiary->contact_number) }}"
                        placeholder="09XXXXXXXXX"
                        maxlength="11"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
            </div>
        </div>

        {{-- Section 3: Family --}}
        <div class="interview-section">
            <div class="interview-section-title">Family Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Family Size</label>
                    <input type="number" name="family_size"
                        value="{{ old('family_size', $beneficiary->family_size) }}" required
                        min="1" max="20">
                </div>
                <div class="form-group">
                    <label>Number of Children (0-17 years)</label>
                    <input type="number" name="children_count"
                        value="{{ old('children_count', $beneficiary->children_count) }}" required
                        min="0" max="20">
                </div>
                <div class="form-group">
                    <label>Monthly Income</label>
                    <input type="number" name="monthly_income"
                        value="{{ old('monthly_income', $beneficiary->monthly_income) }}" required
                        min="0" step="100">
                </div>
                <div class="form-group">
                    <label>4Ps Member</label>
                    <select name="is_4ps_member" required>
                        <option value="">Select Option</option>
                        <option value="1" {{ old('is_4ps_member', $beneficiary->is_4ps_member) == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('is_4ps_member', $beneficiary->is_4ps_member) == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Has Senior Citizen (60+ years)</label>
                    <select name="has_senior" required>
                        <option value="">Select Option</option>
                        <option value="1" {{ old('has_senior', $beneficiary->has_senior) == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('has_senior', $beneficiary->has_senior) == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Section 4: Notes --}}
        <div class="interview-section">
            <div class="interview-section-title">Interview Notes</div>
            <div class="form-grid">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Additional Notes (Optional)</label>
                    <textarea name="interview_notes" rows="4"
                        placeholder="Any additional observations or notes...">{{ old('interview_notes', $beneficiary->interview_notes) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="form-actions">
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Update Beneficiary
            </button>
            <a href="{{ route('admin.beneficiaries.index') }}" class="btn-cancel">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<script>
// Municipality-Barangay dependency
document.getElementById('municipality').addEventListener('change', function() {
    const municipalityId = this.value;
    const barangaySelect = document.getElementById('barangay');
    
    // Clear current barangay options
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    
    if (municipalityId) {
        // Fetch barangays for selected municipality
        fetch(`/api/barangays/${municipalityId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(barangay => {
                    const option = document.createElement('option');
                    option.value = barangay.id;
                    option.textContent = barangay.name;
                    barangaySelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching barangays:', error));
    }
});
</script>
@endsection
