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
        <x-back-button href="{{ route('admin.inventory.index') }}" label="Back" />
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
                <div class="inventory-card-placeholder" style="background: {{ $item->color ?? collect(['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316', '#6366F1', '#84CC16', '#06B6D4', '#A855F7', '#DC2626', '#059669', '#7C3AED', '#DB2777', '#0891B2', '#EA580C', '#4F46E5', '#BE185D', '#047857'])[abs(crc32($item->name . $item->id)) % 21] }};">
                    {{ strtoupper($item->name) }}
                </div>
            @endif
        </div>
        <div class="inventory-item-body">
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

@push('styles')
<style>
/* =============================================
   ITEM CARDS
   ============================================= */
.inventory-item-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    transition: transform .2s ease, box-shadow .2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
    display: flex;
    flex-direction: column;
}

.inventory-item-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,.12);
}

.inventory-card-img {
    height: 130px;
    overflow: hidden;
    position: relative;
    margin: 0;
    padding: 0;
}

.inventory-card-placeholder {
    width: 100%;
    height: 130px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: clamp(0.875rem, 3vw, 1.25rem);
    text-shadow: 0 1px 2px rgba(0,0,0,.3);
    letter-spacing: 0.3px;
    text-align: center;
    padding: 0.75rem;
    word-wrap: break-word;
    line-height: 1.1;
    max-width: 100%;
    overflow: hidden;
    position: absolute;
    top: 0;
    left: 0;
}

.inventory-item-card:hover .inventory-card-placeholder {
    transform: scale(1.03);
    transition: transform .2s ease;
}

.inventory-item-body {
    padding: 1rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.inventory-card-name {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
}

.inventory-card-desc {
    font-size: 0.78rem;
    color: #9ca3af;
    line-height: 1.4;
}

.inventory-item-meta {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    margin-top: 0.25rem;
}

.meta-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    gap: 0.5rem;
}

.meta-label {
    color: #6b7280;
    font-weight: 500;
    flex-shrink: 0;
}

.meta-value {
    color: #1f2937;
    font-weight: 600;
    text-align: right;
    word-break: break-word;
}

.inventory-card-actions {
    padding: 0.75rem 1rem 1rem;
    display: flex;
    gap: 0.5rem;
    border-top: 1px solid #f3f4f6;
}
</style>
@endpush