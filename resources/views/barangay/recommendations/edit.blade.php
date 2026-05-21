@extends('admin.layouts.app')
@section('title', 'Edit Recommendation')
@section('breadcrumb', 'Recommendations > Edit')

@section('content')
<div class="dash-header">
    <h1>Edit Recommendation</h1>
    <x-back-button href="{{ route('barangay.recommendations.index') }}" />
</div>

<div class="section-card">
    <form method="POST" action="{{ route('barangay.recommendations.update', $recommendation->id) }}">
        @csrf
        @method('PUT')
        
        <div class="form-grid">
            <div class="form-section">
                <h3>Personal Information</h3>
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="full_name" required value="{{ old('full_name', $recommendation->full_name) }}">
                </div>
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number', $recommendation->contact_number) }}">
                </div>
                <div class="form-group">
                    <label>Address *</label>
                    <textarea name="address" required rows="3">{{ old('address', $recommendation->address) }}</textarea>
                </div>
                <div class="form-group">
                    <label>Age *</label>
                    <input type="number" name="age" required min="1" max="120" value="{{ old('age', $recommendation->age) }}">
                </div>
                <div class="form-group">
                    <label>Gender *</label>
                    <select name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender', $recommendation->gender) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $recommendation->gender) == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Family Size *</label>
                    <input type="number" name="family_size" required min="1" value="{{ old('family_size', $recommendation->family_size) }}">
                </div>
            </div>

            <div class="form-section">
                <h3>Economic Information</h3>
                <div class="form-group">
                    <label>Monthly Income *</label>
                    <input type="number" name="monthly_income" required min="0" value="{{ old('monthly_income', $recommendation->monthly_income) }}">
                </div>
                <div class="form-group">
                    <label>Income Level *</label>
                    <select name="income_level" required>
                        <option value="">Select Income Level</option>
                        <option value="low" {{ old('income_level', $recommendation->income_level) == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="middle" {{ old('income_level', $recommendation->income_level) == 'middle' ? 'selected' : '' }}>Middle</option>
                        <option value="high" {{ old('income_level', $recommendation->income_level) == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>4Ps Member</label>
                    <select name="is_4ps_member">
                        <option value="0" {{ old('is_4ps_member', $recommendation->is_4ps_member) == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('is_4ps_member', $recommendation->is_4ps_member) == '1' ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Has Senior Citizen</label>
                    <select name="has_senior">
                        <option value="0" {{ old('has_senior', $recommendation->has_senior) == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('has_senior', $recommendation->has_senior) == '1' ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Children Count</label>
                    <input type="number" name="children_count" min="0" value="{{ old('children_count', $recommendation->children_count) }}">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>Recommendation Details</h3>
            <div class="form-group">
                <label>Reason for Recommendation *</label>
                <textarea name="reason" required rows="4" placeholder="Please explain why this person should be considered for relief assistance">{{ old('reason', $recommendation->reason) }}</textarea>
            </div>
            <div class="form-group">
                <label>Priority Level *</label>
                <select name="priority_level" required>
                    <option value="">Select Priority</option>
                    <option value="low" {{ old('priority_level', $recommendation->priority_level) == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority_level', $recommendation->priority_level) == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority_level', $recommendation->priority_level) == 'high' ? 'selected' : '' }}>High</option>
                    <option value="critical" {{ old('priority_level', $recommendation->priority_level) == 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>
            <div class="form-group">
                <label>Special Circumstances</label>
                <textarea name="special_circumstances" rows="3" placeholder="Any special circumstances or additional information">{{ old('special_circumstances', $recommendation->special_circumstances) }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Update Recommendation</button>
            <a href="{{ route('barangay.recommendations.show', $recommendation->id) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@if($errors->any())
    <div class="alert-error">
        <strong>Please fix the following errors:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<style>
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.form-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.form-section h3 {
    margin: 0 0 1rem 0;
    color: #2c3e50;
    font-size: 16px;
}

.form-actions {
    display: flex;
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}
</style>
@endsection
