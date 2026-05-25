@extends('admin.layouts.app')

@section('title', 'Create Household')

@section('content')
<div class="dash-header">
    <h1>Create Household</h1>
    <x-back-button href="{{ route('barangay.households.index') }}" label="Back" />
</div>

<div class="form-card">
    <form method="POST" action="{{ route('barangay.households.store') }}" id="householdForm">
        @csrf
        <!-- Head of Household Information -->
        <div class="interview-section">
            <div class="interview-section-title">Head of Household Information</div>
                    
                    <div class="form-row">
            <div class="form-group">
                <label for="head_of_household">Name of Head of Household *</label>
                <input type="text" id="head_of_household" name="head_of_household" 
                       required value="{{ old('head_of_household') }}" placeholder="Enter full name">
                @error('head_of_household')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="age">Age *</label>
                <input type="number" id="age" name="age" 
                       required min="1" max="120" value="{{ old('age') }}" placeholder="Enter age">
                @error('age')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="sex">Sex *</label>
                <select id="sex" name="sex" required>
                    <option value="">Select Sex</option>
                    <option value="male" {{ old('sex') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('sex') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
                @error('sex')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="birthdate">Birthdate *</label>
                <input type="date" id="birthdate" name="birthdate" 
                       required value="{{ old('birthdate') }}" placeholder="Select birthdate">
                @error('birthdate')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="contact_number">Contact Number</label>
                <input type="tel" id="contact_number" name="contact_number" 
                       value="{{ old('contact_number') }}"
                       placeholder="09XXXXXXXXX" maxlength="11"
                       pattern="[0-9]{11}">
                @error('contact_number')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="is_cdc_beneficiary">CDC Beneficiary</label>
                <div class="checkbox-wrapper">
                    <label class="checkbox-label">
                        <input type="checkbox" id="is_cdc_beneficiary" name="is_cdc_beneficiary" 
                               value="1" {{ old('is_cdc_beneficiary') ? 'checked' : '' }}>
                        <span class="checkbox-text">Already a CDC Beneficiary</span>
                    </label>
                </div>
                @error('is_cdc_beneficiary')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="address">Address *</label>
            <textarea id="address" name="address" 
                      rows="3" required placeholder="Enter full address">{{ old('address') }}</textarea>
            @error('address')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- Family Members -->
    <div class="interview-section">
        <div class="interview-section-title">Family Members</div>
                    <div id="membersContainer">
            <div class="family-member-card" data-member-index="0">
                <div class="member-header">
                    <h4>Member 1</h4>
                    <button type="button" class="btn-remove-child" onclick="removeMember(this)">×</button>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" name="members[0][name]" required placeholder="Enter name">
                    </div>
                    <div class="form-group">
                        <label>Age *</label>
                        <input type="number" name="members[0][age]" required min="1" max="120" placeholder="Enter age">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Sex *</label>
                        <select name="members[0][sex]" required>
                            <option value="">Select Sex</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Relationship to Head *</label>
                        <input type="text" name="members[0][relationship_to_head]" required
                               placeholder="e.g., Wife, Son, Daughter, Mother">
                    </div>
                </div>
            </div>
        </div>
        
        <button type="button" class="btn-add-child" onclick="addMember()">
            <i class="fas fa-plus"></i> Add Family Member
        </button>
    </div>

    <div class="form-actions">
        <a href="{{ route('barangay.households.index') }}" class="btn-cancel">Cancel</a>
        <button type="submit" class="btn-submit">
            <i class="fas fa-save"></i> Create Household
        </button>
    </div>
    </form>
</div>
@endsection

@push('styles')
<style>
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
    gap: 2rem;
}

.form-row {
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
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    border: none;
    transition: all 0.2s ease;
}

.btn-cancel {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

.btn-cancel:hover {
    background: #e5e7eb;
}

.btn-submit {
    background: #1a6b2a;
    color: white;
}

.btn-submit:hover {
    background: #27500a;
}

/* Checkbox Styles */
.checkbox-wrapper {
    display: flex;
    align-items: center;
    margin-top: 0.5rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 0.875rem;
    color: #374151;
    font-weight: 500;
}

.checkbox-label input[type="checkbox"] {
    margin-right: 0.75rem;
    width: 16px;
    height: 16px;
    accent-color: #1a6b2a;
    cursor: pointer;
}

.checkbox-text {
    line-height: 1.4;
}

.error-message {
    color: #dc2626;
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-card {
        padding: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
let memberIndex = 1;

function addMember() {
    const container = document.getElementById('membersContainer');
    const memberDiv = document.createElement('div');
    memberDiv.className = 'family-member-card';
    memberDiv.setAttribute('data-member-index', memberIndex);
    
    memberDiv.innerHTML = `
        <div class="member-header">
            <h4>Member ${memberIndex + 1}</h4>
            <button type="button" class="btn-remove-child" onclick="removeMember(this)">×</button>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Name *</label>
                <input type="text" name="members[${memberIndex}][name]" required>
            </div>
            <div class="form-group">
                <label>Age *</label>
                <input type="number" name="members[${memberIndex}][age]" required min="1" max="120">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Sex *</label>
                <select name="members[${memberIndex}][sex]" required>
                    <option value="">Select Sex</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label>Relationship to Head *</label>
                <input type="text" name="members[${memberIndex}][relationship_to_head]" required
                       placeholder="e.g., Wife, Son, Daughter, Mother">
            </div>
        </div>
    `;
    
    container.appendChild(memberDiv);
    memberIndex++;
}

function removeMember(button) {
    const memberItem = button.closest('.family-member-card');
    memberItem.remove();
    
    // Re-index remaining members
    const members = document.querySelectorAll('.family-member-card');
    members.forEach((member, index) => {
        member.setAttribute('data-member-index', index);
        member.querySelector('.member-header h4').textContent = `Member ${index + 1}`;
        
        // Update input names
        const inputs = member.querySelectorAll('input, select');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name && name.startsWith('members[')) {
                const newName = name.replace(/members\[\d+\]/, `members[${index}]`);
                input.setAttribute('name', newName);
            }
        });
    });
    
    memberIndex = members.length;
}

// Set max date for birthdate (today)
document.addEventListener('DOMContentLoaded', function() {
    const birthdateInput = document.getElementById('birthdate');
    if (birthdateInput) {
        const today = new Date().toISOString().split('T')[0];
        birthdateInput.setAttribute('max', today);
    }
    
    // Contact number validation
    const contactNumberInput = document.getElementById('contact_number');
    if (contactNumberInput) {
        contactNumberInput.addEventListener('input', function(e) {
            // Remove any non-digit characters
            let value = e.target.value.replace(/\D/g, '');
            
            // Limit to 11 digits
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            
            // Update the input value
            e.target.value = value;
        });
        
        contactNumberInput.addEventListener('keypress', function(e) {
            // Only allow number keys
            if (e.key < '0' || e.key > '9') {
                e.preventDefault();
            }
        });
        
        // Validate on blur
        contactNumberInput.addEventListener('blur', function(e) {
            const value = e.target.value;
            if (value && value.length !== 11) {
                e.target.setCustomValidity('');
            } else {
                e.target.setCustomValidity('');
            }
        });
    }
});
</script>
@endpush
