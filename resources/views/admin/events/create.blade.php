@extends('admin.layouts.app')
@section('title', 'Create Relief Event')
@section('breadcrumb', '<i class="fas fa-hand-holding-heart"></i> Relief Management / Create Event')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Create Relief Event</h1>
        <p class="page-description">Schedule and organize relief operations for affected barangays</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Events
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

<form method="POST" action="{{ route('admin.events.store') }}" id="reliefEventForm">
    @csrf
    
    <div class="form-grid">
        <!-- Event Details Section -->
        <div class="form-section">
            <div class="section-header">
                <h3><i class="fas fa-calendar-alt"></i> Event Details</h3>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Event Title *</label>
                    <input type="text" id="name" name="name"
                        value="{{ old('name') }}"
                        placeholder="e.g. Relief Operation - Sumacab Este" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"
                        placeholder="Brief description of the relief operation">{{ old('description') }}</textarea>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="date">Date *</label>
                    <input type="date" id="date" name="date"
                        value="{{ old('date') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="time">Time *</label>
                    <input type="time" id="time" name="time"
                        value="{{ old('time') }}" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="venue">Venue *</label>
                <input type="text" id="venue" name="venue"
                    value="{{ old('venue') }}"
                    placeholder="e.g. San Fernando City Hall" required>
            </div>
        </div>

        <!-- Location Assignment Section -->
        <div class="form-section">
            <div class="section-header">
                <h3><i class="fas fa-map-marker-alt"></i> Location Assignment</h3>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="municipality_id">Municipality *</label>
                    <select id="municipality_id" name="municipality_id" required>
                        <option value="">Select Municipality</option>
                        @foreach($municipalities as $municipality)
                            <option value="{{ $municipality->id }}"
                                {{ old('municipality_id') == $municipality->id ? 'selected' : '' }}>
                                {{ $municipality->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="barangay_ids">Barangays *</label>
                    <select id="barangay_ids" name="barangay_ids[]" multiple required>
                        <option value="">Select Municipality first</option>
                    </select>
                    <small class="form-help">Hold Ctrl/Cmd to select multiple barangays</small>
                </div>
            </div>
        </div>

        <!-- Facilitators Section -->
        <div class="form-section">
            <div class="section-header">
                <h3><i class="fas fa-users"></i> Facilitators</h3>
                <button type="button" class="btn btn-sm btn-secondary" onclick="addFacilitator()">
                    <i class="fas fa-plus"></i> Add Facilitator
                </button>
            </div>
            
            <div id="facilitatorsContainer">
                <!-- Facilitators will be added here dynamically -->
            </div>
        </div>

        <!-- Relief Summary Section -->
        <div class="form-section">
            <div class="section-header">
                <h3><i class="fas fa-chart-bar"></i> Relief Summary</h3>
            </div>
            
            <div class="summary-cards">
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-label">Total Beneficiaries</div>
                        <div class="summary-value" id="totalBeneficiaries">0</div>
                    </div>
                </div>
                
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-label">Total Households</div>
                        <div class="summary-value" id="totalHouseholds">0</div>
                    </div>
                </div>
                
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-label">Suggested Relief Packs</div>
                        <div class="summary-value" id="suggestedReliefPacks">0</div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="estimated_beneficiaries">Override Beneficiary Count (Optional)</label>
                <input type="number" id="estimated_beneficiaries" name="estimated_beneficiaries"
                    value="{{ old('estimated_beneficiaries') }}"
                    placeholder="Leave blank to use computed count"
                    min="1">
                <small class="form-help">Leave blank to automatically calculate from selected barangays</small>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-success btn-lg">
            <i class="fas fa-save"></i> Create Relief Event
        </button>
        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> Cancel
        </a>
    </div>
</form>

<!-- Facilitator Template -->
<template id="facilitatorTemplate">
    <div class="facilitator-item" data-facilitator-id="">
        <div class="facilitator-info">
            <div class="form-group">
                <label>Position</label>
                <select name="facilitators[{{ $index }}][position]" class="facilitator-position" required>
                    <option value="">Select Position</option>
                    @foreach($facilitators as $category => $users)
                        <optgroup label="{{ $category }}">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label>Name</label>
                <select name="facilitators[{{ $index }}][user_id]" class="facilitator-user" required>
                    <option value="">Select Person</option>
                    @foreach($facilitators as $category => $users)
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" data-category="{{ $category }}">
                                {{ $user->first_name }} {{ $user->last_name }} ({{ $category }})
                            </option>
                        @endforeach
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="facilitator-actions">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeFacilitator(this)">
                <i class="fas fa-trash"></i> Remove
            </button>
        </div>
    </div>
</template>

@push('scripts')
<script>
let facilitatorIndex = 0;

// Municipality change handler
document.getElementById('municipality_id').addEventListener('change', function() {
    const municipalityId = this.value;
    const barangaySelect = document.getElementById('barangay_ids');
    
    if (municipalityId) {
        fetch(`/admin/barangays/by-municipality/${municipalityId}`)
            .then(response => response.json())
            .then(barangays => {
                barangaySelect.innerHTML = '<option value="">Select Barangays</option>';
                barangays.forEach(barangay => {
                    const option = document.createElement('option');
                    option.value = barangay.id;
                    option.textContent = barangay.name;
                    barangaySelect.appendChild(option);
                });
                updateBeneficiaryCounts();
            });
    } else {
        barangaySelect.innerHTML = '<option value="">Select Municipality first</option>';
        document.getElementById('totalBeneficiaries').textContent = '0';
        document.getElementById('totalHouseholds').textContent = '0';
        document.getElementById('suggestedReliefPacks').textContent = '0';
    }
});

// Barangay selection handler
document.getElementById('barangay_ids').addEventListener('change', updateBeneficiaryCounts);

function updateBeneficiaryCounts() {
    const selectedBarangays = Array.from(document.getElementById('barangay_ids').selectedOptions)
        .map(option => option.value)
        .filter(value => value !== '');
    
    if (selectedBarangays.length > 0) {
        fetch('/admin/beneficiaries/count', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                barangay_ids: selectedBarangays
            })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalBeneficiaries').textContent = data.total;
            document.getElementById('totalHouseholds').textContent = Math.ceil(data.total * 0.8); // Estimate households
            document.getElementById('suggestedReliefPacks').textContent = Math.ceil(data.total * 1.1); // Suggest 10% extra
        });
    } else {
        document.getElementById('totalBeneficiaries').textContent = '0';
        document.getElementById('totalHouseholds').textContent = '0';
        document.getElementById('suggestedReliefPacks').textContent = '0';
    }
}

// Facilitator management
function addFacilitator() {
    const container = document.getElementById('facilitatorsContainer');
    const template = document.getElementById('facilitatorTemplate');
    const clone = template.content.cloneNode(true);
    
    // Update index in template
    const allInputs = clone.querySelectorAll('select, input');
    allInputs.forEach(input => {
        if (input.name && input.name.includes('{{ $index }}')) {
            input.name = input.name.replace('{{ $index }}', facilitatorIndex);
        }
    });
    
    container.appendChild(clone);
    facilitatorIndex++;
}

function removeFacilitator(button) {
    const item = button.closest('.facilitator-item');
    item.remove();
    updateFacilitatorPositions();
}

function updateFacilitatorPositions() {
    const facilitatorItems = document.querySelectorAll('.facilitator-item');
    facilitatorItems.forEach((item, index) => {
        const userSelect = item.querySelector('.facilitator-user');
        const positionSelect = item.querySelector('.facilitator-position');
        
        // Update names to use current index
        const allInputs = item.querySelectorAll('select, input');
        allInputs.forEach(input => {
            if (input.name) {
                input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
            }
        });
    });
}
</script>
@endpush
