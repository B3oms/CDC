@extends('admin.layouts.app')
@section('title', 'Edit Category')

@section('content')
<div class="dash-header">
    <h1>Edit — {{ $category->name }}</h1>
    <x-back-button href="{{ route('admin.inventory.index') }}" label="Back" />
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

    <form method="POST" action="{{ route('admin.inventory.category.update', $category->id) }}"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="name"
                    value="{{ old('name', $category->name) }}" required>
            </div>

            
            <div class="form-group full-width">
                <label>Description</label>
                <textarea name="description" rows="2">{{ old('description', $category->description) }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Save Changes</button>
            <a href="{{ route('admin.inventory.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
