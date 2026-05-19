@extends('admin.layouts.app')
@section('title', 'Add Item')

@section('content')
<div class="dash-header">
    <div>
        <div class="breadcrumb-nav">
            <a href="{{ route('admin.inventory.index') }}">Inventory</a> /
            <a href="{{ route('admin.inventory.category.show', $subcategory->category_id) }}">{{ $subcategory->category->name }}</a> /
            <a href="{{ route('admin.inventory.subcategory', $subcategory->id) }}">{{ $subcategory->name }}</a> /
            <span>Add Item</span>
        </div>
        <h1>Add Item</h1>
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

    <form method="POST" action="{{ route('admin.inventory.item.store', $subcategory->id) }}"
        enctype="multipart/form-data">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label>Item Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    placeholder="e.g. Century Tuna" required>
            </div>
            <div class="form-group">
                <label>Unit</label>
                <input type="text" name="unit" value="{{ old('unit') }}"
                    placeholder="e.g. Can, Box, Sack" required>
            </div>
            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity"
                    value="{{ old('quantity', 0) }}" min="0" required>
            </div>
            <div class="form-group">
                <label>Expiration Date</label>
                <input type="date" name="expiration_date"
                    value="{{ old('expiration_date') }}">
            </div>
            <div class="form-group full-width">
                <label>Description</label>
                <textarea name="description" rows="2"
                    placeholder="Short description">{{ old('description') }}</textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Add Item</button>
            <a href="{{ route('admin.inventory.subcategory', $subcategory->id) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
