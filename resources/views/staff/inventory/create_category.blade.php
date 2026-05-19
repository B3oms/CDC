@extends('staff.layouts.app')
@section('title', 'Add Category')
@section('breadcrumb', 'Add Category')

@section('content')
<div class="dash-header">
    <h1>Add Category</h1>
    <a href="{{ route('staff.inventory.index') }}" class="btn-back">← Back</a>
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

    <form method="POST" action="{{ route('staff.inventory.category.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    placeholder="e.g. Can Goods" required>
            </div>

            <div class="form-group">
                <label>Image</label>
                <input type="file" name="image" accept="image/*" onchange="previewImage(this, 'preview-cat')">
                <img id="preview-cat" src="#" alt="Preview"
                    style="display:none;width:80px;height:80px;object-fit:cover;border-radius:8px;margin-top:8px;">
            </div>

            <div class="form-group full-width">
                <label>Description</label>
                <textarea name="description" rows="2"
                    placeholder="Short description of this category">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Add Category</button>
            <a href="{{ route('staff.inventory.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.form-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 0 auto;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.form-group input,
.form-group textarea {
    padding: 0.75rem;
    border: 1px solid #d3d1c7;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: border-color 0.2s ease;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.alert-error {
    background: #fee2e2;
    border: 1px solid #fecaca;
    color: #dc2626;
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1.5rem;
}

.alert-error ul {
    margin: 0;
    padding-left: 1.5rem;
}
</style>
@endpush

@push('scripts')
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endpush
