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
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Save Changes</button>
            <a href="{{ route('admin.inventory.category.show', $subcategory->category_id) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
