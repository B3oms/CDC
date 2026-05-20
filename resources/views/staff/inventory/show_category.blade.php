@extends('staff.layouts.app')
@section('title', $category->name)
@section('breadcrumb', $category->name)

@section('content')
<div class="dash-header">
    <div class="dash-header-left">
        <div class="category-color-header" style="background-color: {{ $category->color ?? '#10B981' }};">
            <span class="category-color-text">
                {{ strtoupper(substr($category->name, 0, 2)) }}
            </span>
        </div>
        <div>
            <div class="breadcrumb-nav">
                <a href="{{ route('staff.inventory.index') }}">Inventory</a> /
                <span>{{ $category->name }}</span>
            </div>
            <h1>{{ $category->name }}</h1>
        </div>
    </div>
    <div class="dash-header-actions">
        <a href="{{ route('staff.inventory.index') }}" class="btn-back">← Back</a>
        <a href="{{ route('staff.inventory.subcategory.create', $category->id) }}" class="btn-primary">+ Add Subcategory</a>
        <a href="{{ route('staff.inventory.category.edit', $category->id) }}" class="btn-secondary">Edit Category</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($subcategories->isEmpty())
<div class="section-card" style="text-align:center;padding:3rem;">
    <p style="color:#888;">No subcategories yet under {{ $category->name }}.</p>
    <a href="{{ route('staff.inventory.subcategory.create', $category->id) }}"
        class="btn-primary" style="margin-top:1rem;display:inline-block;">
        Add First Subcategory
    </a>
</div>
@else
<div class="inventory-grid">
    @foreach($subcategories as $subcategory)
    <div class="inventory-category-card">
        <a href="{{ route('staff.inventory.subcategory.show', $subcategory->id) }}" class="inventory-card-link">
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
            <a href="{{ route('staff.inventory.subcategory.edit', $subcategory->id) }}" class="btn-sm-secondary">Edit</a>
            <form method="POST" action="{{ route('staff.inventory.subcategory.destroy', $subcategory->id) }}"
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

@push('styles')
<style>
/* =============================================
   HEADER
   ============================================= */
.dash-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.dash-header-left {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
}

.dash-header-left h1 {
    font-size: clamp(1.125rem, 3vw, 1.75rem);
    font-weight: 700;
    color: #1a3d1f;
    margin: 0;
    word-break: break-word;
}

.dash-header-actions {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    flex-wrap: wrap;
    flex-shrink: 0;
}

/* =============================================
   CATEGORY COLOR HEADER BADGE
   ============================================= */
.category-color-header {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,.15);
}

.category-color-text {
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
    text-shadow: 0 1px 2px rgba(0,0,0,.3);
    letter-spacing: .5px;
}

.breadcrumb-nav {
    font-size: 0.78rem;
    color: #9ca3af;
    margin-bottom: 0.2rem;
}

.breadcrumb-nav a {
    color: #6b7280;
    text-decoration: none;
}

.breadcrumb-nav a:hover { text-decoration: underline; }

/* =============================================
   INVENTORY GRID
   4 cols maximized → 3 → 2 → 1
   ============================================= */
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

/* =============================================
   CARDS
   ============================================= */
.inventory-category-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    transition: transform .2s ease, box-shadow .2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
    display: flex;
    flex-direction: column;
}

.inventory-category-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,.12);
}

.inventory-card-link {
    display: block;
    text-decoration: none;
    color: inherit;
    flex: 1;
}

.inventory-card-img {
    height: 130px;
    overflow: hidden;
    position: relative;
}

.inventory-color-container {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform .2s ease;
}

.inventory-color-text {
    color: white;
    font-weight: 700;
    font-size: 1.5rem;
    text-shadow: 0 1px 2px rgba(0,0,0,.3);
    letter-spacing: 1px;
}

.inventory-category-card:hover .inventory-color-container {
    transform: scale(1.03);
}

.inventory-card-body {
    padding: 1rem;
    flex: 1;
}

.inventory-card-name {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.35rem;
}

.inventory-card-count {
    font-size: 0.8rem;
    color: #6b7280;
    margin-bottom: 0.4rem;
}

.inventory-card-desc {
    font-size: 0.78rem;
    color: #9ca3af;
    line-height: 1.4;
}

.inventory-card-actions {
    padding: 0.75rem 1rem 1rem;
    display: flex;
    gap: 0.5rem;
    border-top: 1px solid #f3f4f6;
}

/* =============================================
   RESPONSIVE HEADER & ACTIONS
   ============================================= */
@media (max-width: 768px) {
    .dash-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .dash-header-actions {
        width: 100%;
    }

    .dash-header-actions a {
        flex: 1;
        text-align: center;
        justify-content: center;
    }
}

@media (max-width: 360px) {
    .dash-header-actions {
        flex-direction: column;
    }

    .inventory-card-actions {
        flex-direction: column;
    }

    .btn-sm-secondary,
    .btn-sm-danger {
        width: 100%;
        text-align: center;
    }
}
</style>
@endpush

@endsection