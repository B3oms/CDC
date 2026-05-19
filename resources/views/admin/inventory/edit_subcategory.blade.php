@extends('admin.layouts.app')
@section('title', 'Edit Subcategory')

@section('content')
<div class="dash-header">
    <h1>Edit — {{ $subcategory->name }}</h1>
    <a href="{{ route('admin.inventory.category', $subcategory->category_id) }}" class="btn-back">← Back</a>
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
                <label>Image</label>
                @if($subcategory->image)
                    <img src="{{ asset('storage/' . $subcategory->image) }}"
                        id="preview-sub"
                        style="width:80px;height:80px;object-fit:cover;border-radius:8px;margin-bottom:8px;">
                @else
                    <img id="preview-sub" src="#" alt="Preview"
                        style="display:none;width:80px;height:80px;object-fit:cover;border-radius:8px;margin-bottom:8px;">
                @endif
                <input type="file" name="image" accept="image/*"
                    onchange="previewImage(this, 'preview-sub')">
            </div>
            <div class="form-group full-width">
                <label>Description</label>
                <textarea name="description" rows="2">{{ old('description', $subcategory->description) }}</textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Save Changes</button>
            <a href="{{ route('admin.inventory.category', $subcategory->category_id) }}" class="btn-secondary">Cancel</a>
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