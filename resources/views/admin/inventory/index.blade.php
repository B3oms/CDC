@extends('admin.layouts.app')
@section('title', 'Inventory')
@section('breadcrumb', 'Inventory')

@section('content')
<div class="dash-header">
    <h1>Inventory</h1>
    <a href="{{ route('admin.inventory.category.create') }}" class="btn-primary">+ Add Category</a>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($categories->isEmpty())
<div class="section-card" style="text-align:center;padding:3rem;">
    <p style="color:#888;">No categories yet.</p>
    <a href="{{ route('admin.inventory.category.create') }}"
        class="btn-primary" style="margin-top:1rem;display:inline-block;">
        Add First Category
    </a>
</div>
@else
<div class="inventory-grid">
    @foreach($categories as $category)
    <div class="inventory-category-card">
        <a href="{{ route('admin.inventory.category.show', $category->id) }}" class="inventory-card-link">
            <div class="inventory-card-img">
            <div class="inventory-color-container" style="background-color: {{ $category->color ?? '#10B981' }};">
                <div class="inventory-color-text">
                    {{ strtoupper(substr($category->name, 0, 2)) }}
                </div>
            </div>
        </div>
            <div class="inventory-card-body">
                <div class="inventory-card-name">{{ $category->name }}</div>
                <div class="inventory-card-count">{{ $category->subcategories_count }} subcategories</div>
                @if($category->description)
                    <div class="inventory-card-desc">{{ $category->description }}</div>
                @endif
            </div>
        </a>
        <div class="inventory-card-actions">
            <a href="{{ route('admin.inventory.category.edit', $category->id) }}" class="btn-sm-secondary">Edit</a>
            <form method="POST" action="{{ route('admin.inventory.category.destroy', $category->id) }}"
                style="display:inline;"
                onsubmit="return confirm('Delete {{ $category->name }}?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-sm-danger">Delete</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection

@push('styles')
<style>
.inventory-color-container {
    width: 100%;
    height: 120px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    border: 2px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.inventory-color-container:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.inventory-color-text {
    color: white;
    font-weight: 600;
    font-size: 24px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    letter-spacing: 1px;
}

.inventory-card-img {
    height: 120px;
    margin-bottom: 12px;
}
</style>
@endpush