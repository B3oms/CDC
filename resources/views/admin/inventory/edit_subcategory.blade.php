@extends('admin.layouts.app')
@section('title', 'Edit Subcategory')

@section('content')
<div class="dash-header">
    <h1>Edit — {{ $subcategory->name }}</h1>
    <a href="{{ route('admin.inventory.category.show', $subcategory->category_id) }}" class="btn-back">← Back</a>
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

    <form method="POST" action="{{ route('admin.inventory.subcategory.update', $subcategory->id) }}"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group">
                <label>Subcategory Name</label>
                <input type="text" name="name"
                    value="{{ old('name', $subcategory->name) }}" required>
            </div>
            <div class="form-group">
                <label>Container Color</label>
                <div class="color-picker-wrapper">
                    <input type="color" name="color" id="color-picker" 
                           value="{{ old('color', $subcategory->color ?? '#3B82F6') }}" 
                           class="color-input">
                    <div class="color-preview" id="color-preview" 
                         style="background-color: {{ old('color', $subcategory->color ?? '#3B82F6') }};">
                        <span class="color-hex" id="color-hex">{{ old('color', $subcategory->color ?? '#3B82F6') }}</span>
                    </div>
                </div>
                <small class="form-help">Choose a color for the subcategory container</small>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Save Changes</button>
            <a href="{{ route('admin.inventory.category.show', $subcategory->category_id) }}" class="btn-secondary">Cancel</a>
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
    const colorPicker = document.getElementById('color-picker');
    const colorPreview = document.getElementById('color-preview');
    const colorHex = document.getElementById('color-hex');
    
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
