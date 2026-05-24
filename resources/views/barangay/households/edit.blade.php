@extends('admin.layouts.app')

@section('title', 'Edit Household')

@section('content')
<div class="household-edit-page">
    <div class="page-header">
        <div>
            <a href="{{ route('barangay.households.show', $household) }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Household
            </a>
            <h1>Edit Household</h1>
            <p>Update household information for {{ $household->head_of_household }}</p>
        </div>
    </div>

    <div class="form-container">
        <form method="POST" action="{{ route('barangay.households.update', $household) }}" id="householdForm">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <!-- Head of Household Information -->
                <div class="form-section">
                    <h3>Head of Household Information</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="head_of_household">Name of Head of Household *</label>
                            <input type="text" id="head_of_household" name="head_of_household" 
                                   class="form-control" required value="{{ old('head_of_household', $household->head_of_household) }}">
                            @error('head_of_household')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="age">Age *</label>
                            <input type="number" id="age" name="age" 
                                   class="form-control" required min="1" max="120" value="{{ old('age', $household->age) }}">
                            @error('age')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="sex">Sex *</label>
                            <select id="sex" name="sex" class="form-control" required>
                                <option value="">Select Sex</option>
                                <option value="male" {{ old('sex', $household->sex) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('sex', $household->sex) == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('sex')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="birthdate">Birthdate *</label>
                            <input type="date" id="birthdate" name="birthdate" 
                                   class="form-control" required value="{{ old('birthdate', $household->birthdate->format('Y-m-d')) }}">
                            @error('birthdate')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact_number">Contact Number</label>
                            <input type="tel" id="contact_number" name="contact_number" 
                                   class="form-control" value="{{ old('contact_number', $household->contact_number) }}"
                                   placeholder="09XXXXXXXXX" maxlength="11"
                                   pattern="[0-9]{11}" title="Contact number must be exactly 11 digits">
                            @error('contact_number')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="is_cdc_beneficiary">CDC Beneficiary</label>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="is_cdc_beneficiary" name="is_cdc_beneficiary" 
                                           value="1" {{ old('is_cdc_beneficiary', $household->is_cdc_beneficiary) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    Already a CDC Beneficiary
                                </label>
                            </div>
                            @error('is_cdc_beneficiary')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Address *</label>
                        <textarea id="address" name="address" class="form-control" 
                                  rows="3" required>{{ old('address', $household->address) }}</textarea>
                        @error('address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Family Members -->
                <div class="form-section">
                    <h3>Family Members</h3>
                    <div id="membersContainer">
                        @foreach($household->members as $index => $member)
                            <div class="member-item" data-member-index="{{ $index }}">
                                <div class="member-header">
                                    <span>Member {{ $index + 1 }}</span>
                                    <button type="button" class="btn-remove-member" onclick="removeMember(this)">×</button>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Name *</label>
                                        <input type="text" name="members[{{ $index }}][name]" class="form-control" required
                                               value="{{ old("members.$index.name", $member->name) }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Age *</label>
                                        <input type="number" name="members[{{ $index }}][age]" class="form-control" required min="1" max="120"
                                               value="{{ old("members.$index.age", $member->age) }}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Sex *</label>
                                        <select name="members[{{ $index }}][sex]" class="form-control" required>
                                            <option value="">Select Sex</option>
                                            <option value="male" {{ old("members.$index.sex", $member->sex) == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old("members.$index.sex", $member->sex) == 'female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Relationship to Head *</label>
                                        <input type="text" name="members[{{ $index }}][relationship_to_head]" class="form-control" required
                                               placeholder="e.g., Wife, Son, Daughter, Mother"
                                               value="{{ old("members.$index.relationship_to_head", $member->relationship_to_head) }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <button type="button" class="btn-add-member" onclick="addMember()">
                        <i class="fas fa-plus"></i> Add Family Member
                    </button>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('barangay.households.show', $household) }}" class="btn btn-cancel">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Household
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
.household-edit-page {
    max-width: 100%;
    padding: 0;
}

.page-header {
    margin-bottom: 2rem;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
    text-decoration: none;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.btn-back:hover {
    color: #374151;
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.page-header p {
    color: #6b7280;
    font-size: 1rem;
}

.form-container {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.form-section {
    margin-bottom: 2rem;
}

.form-section h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e5e7eb;
}

.form-grid {
    display: grid;
    gap: 2rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

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

.form-control {
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.875rem;
    transition: border-color 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.error-message {
    color: #ef4444;
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.form-hint {
    color: #6b7280;
    font-size: 0.75rem;
    margin-top: 0.25rem;
    font-style: italic;
}

.checkbox-group {
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
}

.checkbox-label input[type="checkbox"] {
    margin-right: 0.5rem;
}

.member-item {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    background: #f9fafb;
}

.member-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    font-weight: 500;
    color: #374151;
}

.btn-remove-member {
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 14px;
}

.btn-remove-member:hover {
    background: #dc2626;
}

.btn-add-member {
    background: #10b981;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    cursor: pointer;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
}

.btn-add-member:hover {
    background: #059669;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: none;
}

.btn-cancel {
    background: #f3f4f6;
    color: #374151;
}

.btn-cancel:hover {
    background: #e5e7eb;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-container {
        padding: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
let memberIndex = {{ $household->members->count() }};

function addMember() {
    const container = document.getElementById('membersContainer');
    const memberDiv = document.createElement('div');
    memberDiv.className = 'member-item';
    memberDiv.setAttribute('data-member-index', memberIndex);
    
    memberDiv.innerHTML = `
        <div class="member-header">
            <span>Member ${memberIndex + 1}</span>
            <button type="button" class="btn-remove-member" onclick="removeMember(this)">×</button>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Name *</label>
                <input type="text" name="members[${memberIndex}][name]" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Age *</label>
                <input type="number" name="members[${memberIndex}][age]" class="form-control" required min="1" max="120">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Sex *</label>
                <select name="members[${memberIndex}][sex]" class="form-control" required>
                    <option value="">Select Sex</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label>Relationship to Head *</label>
                <input type="text" name="members[${memberIndex}][relationship_to_head]" class="form-control" required
                       placeholder="e.g., Wife, Son, Daughter, Mother">
            </div>
        </div>
    `;
    
    container.appendChild(memberDiv);
    memberIndex++;
}

function removeMember(button) {
    const memberItem = button.closest('.member-item');
    memberItem.remove();
    
    // Re-index remaining members
    const members = document.querySelectorAll('.member-item');
    members.forEach((member, index) => {
        member.setAttribute('data-member-index', index);
        member.querySelector('.member-header span').textContent = `Member ${index + 1}`;
        
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
                e.target.setCustomValidity('Contact number must be exactly 11 digits');
            } else {
                e.target.setCustomValidity('');
            }
        });
    }
});
</script>
@endpush
