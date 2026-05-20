@extends('staff.layouts.app')
@section('title', 'Inventory')
@section('breadcrumb', 'Inventory')

@section('content')
<div class="dash-header">
    <h1>Inventory</h1>
    <div style="display: flex; gap: 10px; align-items: center;">
        <a href="{{ route('staff.inventory.pdf') }}" class="btn-export-pdf" target="_blank"
           style="display: inline-flex !important; align-items: center !important; gap: 6px !important; padding: 8px 16px !important; background: #10b981 !important; color: white !important; text-decoration: none !important; border-radius: 6px !important; font-size: 13px !important; font-weight: 500 !important; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3) !important; letter-spacing: 0.5px !important;"
           onmouseover="this.style.background='#059669'"
           onmouseout="this.style.background='#10b981'">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('staff.inventory.category.create') }}" class="btn-primary" style="text-decoration: none;">+ Add Category</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($categories->isEmpty())
<div class="section-card" style="text-align:center;padding:3rem;">
    <p style="color:#888;">No categories yet.</p>
    <a href="{{ route('staff.inventory.category.create') }}"
        class="btn-primary" style="margin-top:1rem;display:inline-block;">
        Add First Category
    </a>
</div>
@else
<div class="inventory-grid">
    @foreach($categories as $category)
    <div class="inventory-category-card">
        <a href="{{ route('staff.inventory.category.show', $category->id) }}" class="inventory-card-link">
            <div class="inventory-card-img">
                @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                @else
                    <div class="inventory-card-placeholder">
                        {{ strtoupper(substr($category->name, 0, 2)) }}
                    </div>
                @endif
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
            <form method="GET" action="{{ route('inventory.category.edit', $category->id) }}" style="display:inline;">
                <button type="submit" class="btn-sm-secondary">Edit</button>
            </form>
            <form method="POST" action="{{ route('staff.inventory.category.destroy', $category->id) }}"
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
.btn-export-pdf {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 8px 16px !important;
    background: #10b981 !important;
    color: white !important;
    text-decoration: none !important;
    border-radius: 6px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3) !important;
    letter-spacing: 0.5px !important;
}

.btn-export-pdf:hover {
    background: #059669 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 8px 12px -2px rgba(16, 185, 129, 0.4) !important;
    text-decoration: none !important;
    color: white !important;
}
</style>
@endpush
