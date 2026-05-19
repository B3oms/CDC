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
/* Inventory Index Specific Responsive Styles */
@media (max-width: 1024px) {
    .dash-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .dash-header h1 {
        text-align: center;
        font-size: 1.5rem;
    }
    
    .btn-primary {
        align-self: center;
        max-width: 200px;
    }
    
    .inventory-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }
    
    .inventory-category-card {
        margin-bottom: 0;
    }
    
    .inventory-card-img {
        height: 100px;
    }
    
    .inventory-color-text {
        font-size: 20px;
    }
    
    .inventory-card-body {
        padding: 1rem;
    }
    
    .inventory-card-name {
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .inventory-card-count {
        font-size: 0.85rem;
    }
    
    .section-card {
        padding: 2rem;
    }
}

@media (max-width: 768px) {
    .dash-header {
        padding: 1rem;
    }
    
    .dash-header h1 {
        font-size: 1.25rem;
    }
    
    .btn-primary {
        padding: 12px 16px;
        font-size: 0.9rem;
    }
    
    .inventory-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem;
    }
    
    .inventory-card-img {
        height: 80px;
    }
    
    .inventory-color-text {
        font-size: 18px;
    }
    
    .inventory-card-body {
        padding: 0.75rem;
    }
    
    .inventory-card-name {
        font-size: 0.9rem;
    }
    
    .inventory-card-count {
        font-size: 0.8rem;
    }
    
    .inventory-card-desc {
        font-size: 0.75rem;
    }
    
    .inventory-card-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .inventory-card-actions .btn {
        width: 100%;
        font-size: 0.8rem;
        padding: 8px 12px;
    }
    
    .section-card {
        padding: 1.5rem;
    }
    
    .section-card p {
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    .dash-header {
        padding: 0.75rem;
    }
    
    .dash-header h1 {
        font-size: 1.1rem;
    }
    
    .btn-primary {
        padding: 10px 14px;
        font-size: 0.85rem;
    }
    
    .inventory-grid {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .inventory-category-card {
        border-radius: 8px;
    }
    
    .inventory-card-img {
        height: 60px;
    }
    
    .inventory-color-text {
        font-size: 16px;
    }
    
    .inventory-card-body {
        padding: 0.5rem;
    }
    
    .inventory-card-name {
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
    }
    
    .inventory-card-count {
        font-size: 0.75rem;
    }
    
    .inventory-card-desc {
        font-size: 0.7rem;
    }
    
    .inventory-card-actions {
        padding: 0.5rem;
        gap: 0.25rem;
    }
    
    .inventory-card-actions .btn {
        font-size: 0.75rem;
        padding: 6px 10px;
    }
    
    .section-card {
        padding: 1rem;
    }
    
    .section-card p {
        font-size: 0.8rem;
    }
    
    .alert-success {
        padding: 0.75rem;
        font-size: 0.8rem;
    }
}

/* Landscape Mobile for Inventory */
@media (max-width: 768px) and (orientation: landscape) {
    .dash-header {
        flex-direction: row;
        align-items: center;
        padding: 0.5rem 1rem;
    }
    
    .dash-header h1 {
        font-size: 1rem;
    }
    
    .btn-primary {
        padding: 8px 12px;
        font-size: 0.8rem;
    }
    
    .inventory-grid {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }
    
    .inventory-card-img {
        height: 50px;
    }
    
    .inventory-color-text {
        font-size: 14px;
    }
    
    .inventory-card-body {
        padding: 0.5rem;
    }
}

/* Tablet Portrait for Inventory */
@media (min-width: 768px) and (max-width: 1024px) and (orientation: portrait) {
    .inventory-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .inventory-card-img {
        height: 90px;
    }
    
    .inventory-color-text {
        font-size: 22px;
    }
}
</style>
@endpush

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