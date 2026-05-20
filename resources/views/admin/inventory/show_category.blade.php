@extends('admin.layouts.app')
@section('title', $category->name)

@section('content')
<div class="dash-header">
    <div style="display:flex;align-items:center;gap:14px;">
        <div class="category-color-header" style="background-color: {{ $category->color ?? '#10B981' }};">
            <span class="category-color-text">
                {{ strtoupper(substr($category->name, 0, 2)) }}
            </span>
        </div>
        <div>
            <div class="breadcrumb-nav">
                <a href="{{ route('admin.inventory.index') }}">Inventory</a> /
                <span>{{ $category->name }}</span>
            </div>
            <h1>{{ $category->name }}</h1>
        </div>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('admin.inventory.index') }}" class="btn-back">← Back</a>
        <a href="{{ route('admin.inventory.subcategory.create', $category->id) }}" class="btn-primary">+ Add Subcategory</a>
        <a href="{{ route('admin.inventory.category.edit', $category->id) }}" class="btn-secondary">Edit Category</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($subcategories->isEmpty())
<div class="section-card" style="text-align:center;padding:3rem;">
    <p style="color:#888;">No subcategories yet under {{ $category->name }}.</p>
    <a href="{{ route('admin.inventory.subcategory.create', $category->id) }}"
        class="btn-primary" style="margin-top:1rem;display:inline-block;">
        Add First Subcategory
    </a>
</div>
@else
<div class="inventory-grid">
    @foreach($subcategories as $subcategory)
    <div class="inventory-category-card">
        <a href="{{ route('admin.inventory.subcategory', $subcategory->id) }}" class="inventory-card-link">
            <div class="inventory-card-img">
            <div class="inventory-color-container" style="background-color: {{ $subcategory->color ?? '#3B82F6' }};">
                <div class="inventory-color-text">
                    {{ strtoupper(substr($subcategory->name, 0, 2)) }}
                </div>
            </div>
        </div>
            <div class="inventory-card-body">
                <div class="inventory-card-name">{{ $subcategory->name }}</div>
                <div class="inventory-card-count">{{ $subcategory->items_count }} items</div>
                @if($subcategory->description)
                    <div class="inventory-card-desc">{{ $subcategory->description }}</div>
                @endif
            </div>
        </a>
        <div class="inventory-card-actions">
            <a href="{{ route('admin.inventory.subcategory.edit', $subcategory->id) }}" class="btn-sm-secondary">Edit</a>
            <form method="POST" action="{{ route('admin.inventory.subcategory.destroy', $subcategory->id) }}"
                style="display:inline;"
                onsubmit="return confirm('Delete {{ $subcategory->name }}?')">
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
.inventory-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
}

@media (max-width: 1200px) {
    .inventory-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
    }
}

@media (max-width: 860px) {
    .inventory-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
}

@media (max-width: 480px) {
    .inventory-grid {
        grid-template-columns: 1fr;
        gap: 0.875rem;
    }
}

.category-color-header {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.category-color-text {
    color: white;
    font-weight: 600;
    font-size: 18px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    letter-spacing: 1px;
}

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

@media (max-width: 768px) {
    .dash-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .dash-header > div:last-child {
        width: 100%;
        flex-wrap: wrap;
    }

    .inventory-card-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>
@endpush