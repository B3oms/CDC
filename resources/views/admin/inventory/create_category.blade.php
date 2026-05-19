@extends('admin.layouts.app')
@section('title', 'Add Category')

@section('content')
<div class="dash-header">
    <h1>Add Category</h1>
    <a href="{{ route('admin.inventory.index') }}" class="btn-back">← Back</a>
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

    <form method="POST" action="{{ route('admin.inventory.category.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    placeholder="e.g. Can Goods" required>
            </div>

            
            <div class="form-group full-width">
                <label>Description</label>
                <textarea name="description" rows="2"
                    placeholder="Short description of this category">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Add Category</button>
            <a href="{{ route('admin.inventory.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

