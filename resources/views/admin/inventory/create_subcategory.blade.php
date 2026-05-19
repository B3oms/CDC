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
