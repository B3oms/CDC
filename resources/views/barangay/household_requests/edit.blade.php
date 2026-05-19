@extends('admin.layouts.app')
@section('title', 'Edit Household Request')
@section('breadcrumb', 'Edit Household Request')

@section('content')
<div class="dash-header">
    <div>
        <h1>Edit Household Request</h1>
        <p class="sub">Update household assistance request</p>
    </div>
    <a href="{{ route('barangay.household_requests.show', $request->id) }}" class="btn-back">← Back</a>
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

    <form method="POST" action="{{ route('barangay.household_requests.update', $request->id) }}">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group full-width">
                <h3>Head of Household Information</h3>
            </div>
            
            <div class="form-group">
                <label>Name of Head of Household *</label>
                <input type="text" name="head_name" value="{{ old('head_name', $request->head_of_household) }}"
                    placeholder="e.g. Juan Dela Cruz" required>
            </div>
            
            <div class="form-group">
                <label>Age *</label>
                <input type="number" name="head_age" value="{{ old('head_age', $request->head_age) }}" min="1" max="120" required placeholder="Age">
            </div>
            
            <div class="form-group">
                <label>Sex *</label>
                <select name="head_sex" required>
                    <option value="">Select Sex</option>
                    <option value="male" {{ old('head_sex', $request->head_sex) == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('head_sex', $request->head_sex) == 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
                        
            <div class="form-group">
                <label>Date of Birth *</label>
                <input type="date" name="head_date_of_birth" value="{{ old('head_date_of_birth', $request->birthday ? $request->birthday->format('Y-m-d') : '') }}" required>
            </div>
            
            <div class="form-group">
                <label>Contact Number *</label>
                <input type="text" name="contact_number" value="{{ old('contact_number', $request->contact_number) }}"
                    placeholder="Contact number" required>
            </div>
            
            <div class="form-group full-width">
                <label>Address *</label>
                <textarea name="address" rows="3" required
                    placeholder="Complete household address">{{ old('address', $request->address) }}</textarea>
            </div>
            
            <div class="form-group full-width">
                <h3>Family Members</h3>
                <p>Add other family members living in the household (excluding the head of household)</p>
            </div>
        </div>
        
        <div id="family-members-container">
            @foreach($request->members as $index => $member)
            <div class="form-grid family-member-row" style="margin-bottom: 20px; border: 1px solid #e5e7eb; padding: 15px; border-radius: 8px;">
                <div class="form-group">
                    <label>Member Name</label>
                    <input type="text" name="members[{{ $index }}][name]" value="{{ old('members.'.$index.'.name', $member->name) }}" placeholder="Full name">
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="members[{{ $index }}][age]" value="{{ old('members.'.$index.'.age', $member->age) }}" min="1" max="120" placeholder="Age">
                </div>
                <div class="form-group">
                    <label>Sex</label>
                    <select name="members[{{ $index }}][sex]">
                        <option value="">Select Sex</option>
                        <option value="male" {{ old('members.'.$index.'.sex', $member->sex) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('members.'.$index.'.sex', $member->sex) == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end;">
                    <button type="button" onclick="removeMember(this)" class="btn-sm-danger">Remove</button>
                </div>
            </div>
            @endforeach
        </div>
        
        <div style="margin: 20px 0;">
            <button type="button" onclick="addFamilyMember()" class="btn-secondary">
                <i class="fas fa-plus"></i> Add Family Member
            </button>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">Update Request</button>
            <a href="{{ route('barangay.household_requests.show', $request->id) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
let memberCount = {{ $request->members->count() }};

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
