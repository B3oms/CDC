@extends('admin.layouts.app')
@section('title', 'New Household Request')
@section('breadcrumb', 'New Household Request')

@section('content')
<div class="dash-header">
    <div>
        <h1>New Household Request</h1>
        <p class="sub">Submit a household assistance request</p>
    </div>
    <x-back-button href="{{ route('barangay.household_requests.index') }}" label="Back" />
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

    <form method="POST" action="{{ route('barangay.household_requests.store') }}">
        @csrf
        <div class="form-grid">
            <div class="form-group full-width">
                <h3>Head of Household Information</h3>
            </div>
            
            <div class="form-group">
                <label>Name of Head of Household *</label>
                <input type="text" name="head_name" value="{{ old('head_name') }}"
                    placeholder="e.g. Juan Dela Cruz" required>
            </div>
            
            <div class="form-group">
                <label>Age *</label>
                <input type="number" name="head_age" value="{{ old('head_age') }}" min="1" max="120" required placeholder="Age">
            </div>
            
            <div class="form-group">
                <label>Sex *</label>
                <select name="head_sex" required>
                    <option value="">Select Sex</option>
                    <option value="male" {{ old('head_sex') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('head_sex') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
                        
            <div class="form-group">
                <label>Date of Birth *</label>
                <input type="date" name="head_date_of_birth" value="{{ old('head_date_of_birth') }}" required>
            </div>
            
            <div class="form-group">
                <label>Contact Number *</label>
                <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                    placeholder="Contact number" required>
            </div>
            
            <div class="form-group full-width">
                <label>Address *</label>
                <textarea name="address" rows="3" required
                    placeholder="Complete household address">{{ old('address') }}</textarea>
            </div>
            
            <div class="form-group full-width">
                <h3>Family Members</h3>
                <p>Add other family members living in the household (excluding the head of household)</p>
            </div>
        </div>
        
        <div id="family-members-container">
            <div class="form-grid family-member-row" style="margin-bottom: 20px; border: 1px solid #e5e7eb; padding: 15px; border-radius: 8px;">
                <div class="form-group">
                    <label>Member Name</label>
                    <input type="text" name="members[0][name]" placeholder="Full name">
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="members[0][age]" min="1" max="120" placeholder="Age">
                </div>
                <div class="form-group">
                    <label>Sex</label>
                    <select name="members[0][sex]">
                        <option value="">Select Sex</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end;">
                    <button type="button" onclick="removeMember(this)" class="btn-sm-danger" style="display: none;">Remove</button>
                </div>
            </div>
        </div>
        
        <div style="margin: 20px 0;">
            <button type="button" onclick="addFamilyMember()" class="btn-secondary">
                <i class="fas fa-plus"></i> Add Family Member
            </button>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">Submit Request</button>
            <a href="{{ route('barangay.household_requests.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
let memberCount = 1;

function addFamilyMember() {
    const container = document.getElementById('family-members-container');
    const newRow = document.createElement('div');
    newRow.className = 'form-grid family-member-row';
    newRow.style.cssText = 'margin-bottom: 20px; border: 1px solid #e5e7eb; padding: 15px; border-radius: 8px;';
    
    newRow.innerHTML = `
        <div class="form-group">
            <label>Member Name</label>
            <input type="text" name="members[${memberCount}][name]" placeholder="Full name">
        </div>
        <div class="form-group">
            <label>Age</label>
            <input type="number" name="members[${memberCount}][age]" min="1" max="120" placeholder="Age">
        </div>
        <div class="form-group">
            <label>Sex</label>
            <select name="members[${memberCount}][sex]">
                <option value="">Select Sex</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
        <div class="form-group" style="display: flex; align-items: flex-end;">
            <button type="button" onclick="removeMember(this)" class="btn-sm-danger">Remove</button>
        </div>
    `;
    
    container.appendChild(newRow);
    memberCount++;
}

function removeMember(button) {
    const row = button.closest('.family-member-row');
    row.remove();
}
</script>
@endsection
