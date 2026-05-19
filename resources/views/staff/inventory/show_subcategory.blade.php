@extends('staff.layouts.app')
@section('title', $subcategory->name)
@section('breadcrumb', $subcategory->name)

@section('content')
<div class="dash-header">
    <div>
        <div class="breadcrumb-nav">
            <a href="{{ route('staff.inventory.index') }}">Inventory</a> /
            <a href="{{ route('staff.inventory.category.show', $subcategory->category_id) }}">{{ $subcategory->category->name }}</a> /
            <span>{{ $subcategory->name }}</span>
        </div>
        <h1>{{ $subcategory->name }}</h1>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('staff.inventory.category.show', $subcategory->category_id) }}" class="btn-back">← Back</a>
        <a href="{{ route('staff.inventory.item.create', $subcategory->id) }}" class="btn-primary">+ Add Item</a>
        <a href="{{ route('staff.inventory.subcategory.edit', $subcategory->id) }}" class="btn-secondary">Edit Subcategory</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($items->isEmpty())
<div class="section-card" style="text-align:center;padding:3rem;">
    <p style="color:#888;">No items yet under {{ $subcategory->name }}.</p>
    <a href="{{ route('staff.inventory.item.create', $subcategory->id) }}"
        class="btn-primary" style="margin-top:1rem;display:inline-block;">
        Add First Item
    </a>
</div>
@else
<div class="inventory-grid">
    @foreach($items as $item)
    <div class="inventory-item-card">
        <div class="inventory-card-img">
            @if($item->image)
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
            @else
                <div class="inventory-card-placeholder">
                    {{ strtoupper(substr($item->name, 0, 2)) }}
                </div>
            @endif
        </div>
        <div class="inventory-item-body">
            <div class="inventory-card-name">{{ $item->name }}</div>
            @if($item->description)
                <div class="inventory-card-desc">{{ $item->description }}</div>
            @endif
            <div class="inventory-item-meta">
                <div class="meta-row">
                    <span class="meta-label">Quantity</span>
                    <span class="{{ ($item->inventory?->quantity ?? 0) <= 10 ? 'text-danger' : '' }}">
                        {{ $item->inventory?->quantity ?? 0 }} {{ $item->unit }}
                    </span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Expires</span>
                    <span>
                        @if($item->inventory)
                            @if($item->inventory->expiration_date)
                                {{ \Carbon\Carbon::parse($item->inventory->expiration_date)->format('M d, Y') }}
                            @else
                                No expiry
                            @endif
                        @else
                            No inventory record
                        @endif
                    </span>
                </div>
            </div>
        </div>
        <div class="inventory-card-actions">
            <a href="{{ route('staff.inventory.item.edit', $item->id) }}" class="btn-sm-secondary">Edit</a>
            <form method="POST" action="{{ route('staff.inventory.item.destroy', $item->id) }}"
                style="display:inline;"
                onsubmit="return confirm('Delete {{ $item->name }}?')">
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
