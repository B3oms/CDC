@extends('admin.layouts.app')
@section('title', 'Edit Item')

@section('content')
<div class="dash-header">
    <div>
        <div class="breadcrumb-nav">
            <a href="{{ route('admin.inventory.index') }}">Inventory</a> /
            <a href="{{ route('admin.inventory.category.show', $subcategory->category_id) }}">{{ $subcategory->category->name }}</a> /
            <a href="{{ route('admin.inventory.subcategory', $subcategory->id) }}">{{ $subcategory->name }}</a> /
            <span>Edit Item</span>
        </div>
        <h1>Edit — {{ $item->name }}</h1>
    </div>
    <a href="{{ route('admin.inventory.subcategory', $subcategory->id) }}" class="btn-back">← Back</a>
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

    <form method="POST" action="{{ route('admin.inventory.item.update', $item->id) }}"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group">
                <label>Item Name</label>
                <input type="text" name="name"
                    value="{{ old('name', $item->name) }}" required>
            </div>
            <div class="form-group">
                <label>Unit</label>
                <input type="text" name="unit"
                    value="{{ old('unit', $item->unit) }}" required>
            </div>
            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity"
                    value="{{ old('quantity', $item->inventory?->quantity ?? 0) }}"
                    min="0" required>
            </div>
            <div class="form-group">
                <label>Expiration Date</label>
                <input type="date" name="expiration_date"
                    value="{{ old('expiration_date', $item->inventory?->expiration_date ?? '') }}">
            </div>
            <div class="form-group">
                <label>Item Image</label>
                @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}"
                        id="preview-item"
                        style="width:80px;height:80px;object-fit:cover;border-radius:8px;margin-bottom:8px;">
                @else
                    <img id="preview-item" src="#" alt="Preview"
                        style="display:none;width:80px;height:80px;object-fit:cover;border-radius:8px;margin-bottom:8px;">
                @endif
                <input type="file" name="image" accept="image/*"
                    onchange="previewImage(this, 'preview-item')">
            </div>
            <div class="form-group full-width">
                <label>Description</label>
                <textarea name="description" rows="2">{{ old('description', $item->description) }}</textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Save Changes</button>
            <a href="{{ route('admin.inventory.subcategory', $subcategory->id) }}" class="btn-secondary">Cancel</a>
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