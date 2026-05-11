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
                <label>Image</label>
                <input type="file" name="image" accept="image/*"
                    onchange="previewImage(this, 'preview-sub')">
                <img id="preview-sub" src="#" alt="Preview"
                    style="display:none;width:80px;height:80px;object-fit:cover;border-radius:8px;margin-top:8px;">
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

@push('scripts')
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush