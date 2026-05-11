@extends('admin.layouts.app')
@section('title', $category->name)

@section('content')
<div class="dash-header">
    <div style="display:flex;align-items:center;gap:14px;">
        @if($category->image)
            <img src="{{ asset('storage/' . $category->image) }}"
                style="width:48px;height:48px;border-radius:8px;object-fit:cover;">
        @endif
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
                @if($subcategory->image)
                    <img src="{{ asset('storage/' . $subcategory->image) }}" alt="{{ $subcategory->name }}">
                @else
                    <div class="inventory-card-placeholder">
                        {{ strtoupper(substr($subcategory->name, 0, 2)) }}
                    </div>
                @endif
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