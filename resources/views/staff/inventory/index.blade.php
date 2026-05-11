@extends('staff.layouts.app')
@section('title', 'Inventory')

@section('content')
<div class="dash-header">
    <h1>Inventory Management</h1>
    <a href="{{ route('admin.inventory.create') }}" class="btn-primary">+ Add Item</a>
</div>

{{-- Stats Row --}}
<div class="stats-row" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-num">{{ $totalItems ?? 0 }}</div>
        <div class="stat-label">Total Items</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $lowStockItems ?? 0 }}</div>
        <div class="stat-label">Low Stock</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $categoriesCount ?? 0 }}</div>
        <div class="stat-label">Categories</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $recentItemsCount ?? 0 }}</div>
        <div class="stat-label">Recent Additions</div>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($items->isEmpty())
<div class="section-card" style="text-align:center;padding:3rem;">
    <p style="color:#888;font-size:1rem;">No inventory items yet.</p>
    <a href="{{ route('admin.inventory.create') }}" class="btn-primary" style="margin-top:1rem;display:inline-block;">
        Add First Item
    </a>
</div>
@else

{{-- Categories --}}
@if($categories->count())
<div class="relief-section">
    <div class="relief-section-title">Categories</div>
    <div class="relief-grid">
        @foreach($categories as $category)
        <div class="section-card">
            <h3>{{ $category->name }}</h3>
            <p style="color:#666;font-size:0.9rem;">{{ $category->items_count ?? 0 }} items</p>
            <div style="margin-top:1rem;display:flex;gap:0.5rem;">
                <a href="{{ route('admin.inventory.category.show', $category->id) }}" class="btn-secondary">View Items</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Recent Items --}}
@if($recentItems->count())
<div class="relief-section">
    <div class="relief-section-title">Recent Items</div>
    <div class="relief-grid">
        @foreach($recentItems as $item)
        <div class="section-card">
            <h3>{{ $item->name }}</h3>
            <p style="color:#666;font-size:0.9rem;">Category: {{ $item->category->name }}</p>
            <p style="color:#666;font-size:0.9rem;">Quantity: {{ $item->quantity }}</p>
            <div style="margin-top:1rem;display:flex;gap:0.5rem;">
                <a href="{{ route('admin.inventory.edit', $item->id) }}" class="btn-secondary">Edit</a>
                <form method="POST" action="{{ route('admin.inventory.destroy', $item->id) }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger" onclick="return confirm('Delete this item?')">Delete</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endif
@endsection
