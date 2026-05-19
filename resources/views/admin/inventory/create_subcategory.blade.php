@extends('admin.layouts.app')
@section('title', 'Add Subcategory')

@section('content')
<div class="dash-header">
    <div>
        <div class="breadcrumb-nav">
            <a href="{{ route('admin.inventory.index') }}">Inventory</a> /
            <a href="{{ route('admin.inventory.category.show', $category->id) }}">{{ $category->name }}</a> /
            <span>Add Subcategory</span>
        </div>
        <h1>Add Subcategory</h1>
    </div>
    <a href="{{ route('admin.inventory.category.show', $category->id) }}" class="btn-back">← Back</a>
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

    <form method="POST" action="{{ route('admin.inventory.subcategory.store', $category->id) }}"
        enctype="multipart/form-data">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label>Subcategory Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    placeholder="e.g. Canned Goods" required>
            </div>
            <div class="form-group">
                <label>Container Color</label>
                <div class="color-picker-wrapper">
                    <input type="color" name="color" id="subcategory-color-picker" 
                           value="{{ old('color', '#3B82F6') }}" 
                           class="color-input">
                    <div class="color-preview" id="subcategory-color-preview" 
                         style="background-color: {{ old('color', '#3B82F6') }};">
                        <span class="color-hex" id="subcategory-color-hex">{{ old('color', '#3B82F6') }}</span>
                    </div>
                </div>
                <small class="form-help">Choose a color for the subcategory container</small>
            </div>
            <div class="form-group full-width">
                <label>Description</label>
                <textarea name="description" rows="2"
                    placeholder="Short description">{{ old('description') }}</textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Add Subcategory</button>
            <a href="{{ route('admin.inventory.category.show', $category->id) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.color-picker-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
}

.color-input {
    width: 60px;
    height: 40px;
    border: 2px solid #d3d1c7;
    border-radius: 6px;
    cursor: pointer;
    transition: border-color 0.15s;
}

.color-input:hover {
    border-color: #1a3d1f;
}

.color-preview {
    width: 40px;
    height: 40px;
    border-radius: 6px;
    border: 2px solid #d3d1c7;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.color-hex {
    font-size: 10px;
    font-weight: 600;
    color: white;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

.form-help {
    color: #6b7280;
    font-size: 12px;
    margin-top: 4px;
    display: block;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorPicker = document.getElementById('subcategory-color-picker');
    const colorPreview = document.getElementById('subcategory-color-preview');
    const colorHex = document.getElementById('subcategory-color-hex');
    
    if (colorPicker && colorPreview && colorHex) {
        colorPicker.addEventListener('input', function() {
            const color = this.value;
            colorPreview.style.backgroundColor = color;
            colorHex.textContent = color.toUpperCase();
        });
    }
});
</script>
@endpush