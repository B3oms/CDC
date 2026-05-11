@extends('admin.layouts.app')
@section('title', '{{ $category->name }}')

@section('content')
<div class="dash-header">
    <div style="display:flex;align-items:center;gap:14px;">
        @if($category->image)
            <img src="{{ asset('storage/' . $category->image) }}"
                style="width:48px;height:48px;border-radius:8px;object-fit:cover;">
        @endif
        <h1>{{ $category->name }}</h1>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('admin.inventory.index') }}" class="btn-back">← Back</a>
        <a href="{{ route('admin.inventory.item.create', $category->id) }}" class="btn-primary">+ Add Item</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($items->isEmpty())
<div class="section-card" style="text-align:center;padding:3rem;">
    <p style="color:#888;">No items yet in this category.</p>
    <a href="{{ route('admin.inventory.item.create', $category->id) }}"
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
            <<div class="inventory-item-meta">
    <div class="meta-row">
        <span class="meta-label">Quantity</span>
        <span class="{{ ($item->inventory?->quantity ?? 0) <= 10 ? 'text-danger' : '' }}">
            {{ $item->inventory?->quantity ?? 0 }} {{ $item->unit }}
        </span>
    </div>
    <div class="meta-row">
        <span class="meta-label">Expires</span>
        <span>
            {{ $item->inventory?->expiration_date
                ? \Carbon\Carbon::parse($item->inventory->expiration_date)->format('M d, Y')
                : 'N/A' }}
        </span>
    </div>
</div>
        <div class="inventory-card-actions">
            <a href="{{ route('admin.inventory.item.edit', $item->id) }}" class="btn-sm-secondary">Edit</a>
            <form method="POST" action="{{ route('admin.inventory.item.destroy', $item->id) }}"
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