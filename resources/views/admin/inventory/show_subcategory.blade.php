@extends('admin.layouts.app')
@section('title', $subcategory->name)

@section('content')
<div class="dash-header">
    <div>
        <div class="breadcrumb-nav">
            <a href="{{ route('admin.inventory.index') }}">Inventory</a> /
            <a href="{{ route('admin.inventory.category.show', $subcategory->category_id) }}">{{ $subcategory->category->name }}</a> /
            <span>{{ $subcategory->name }}</span>
        </div>
        <h1>{{ $subcategory->name }}</h1>
    </div>
    <div style="display:flex;gap:10px;">
        <x-back-button href="{{ route('admin.inventory.category.show', $subcategory->category_id) }}" label="Back" />
        <a href="{{ route('admin.inventory.item.create', $subcategory->id) }}" class="btn-primary">+ Add Item</a>
        <a href="{{ route('admin.inventory.subcategory.edit', $subcategory->id) }}" class="btn-secondary">Edit Subcategory</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($items->isEmpty())
<div class="section-card" style="text-align:center;padding:3rem;">
    <p style="color:#888;">No items yet under {{ $subcategory->name }}.</p>
    <a href="{{ route('admin.inventory.item.create', $subcategory->id) }}"
        class="btn-primary" style="margin-top:1rem;display:inline-block;">
        Add First Item
    </a>
</div>
@else
<div class="inventory-grid">
    @foreach($items as $item)
    <div class="inventory-item-card">
        <div class="inventory-card-img">
            <div class="inventory-color-container" style="background-color: {{ $item->color ?? collect(['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316', '#6366F1', '#84CC16', '#06B6D4', '#A855F7', '#DC2626', '#059669', '#7C3AED', '#DB2777', '#0891B2', '#EA580C', '#4F46E5', '#BE185D', '#047857'])[abs(crc32($item->name . $item->id)) % 21] }};">
                <div class="inventory-color-text">
                    {{ strtoupper($item->name) }}
                </div>
            </div>
        </div>
        <div class="inventory-item-body">
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
   HEADER
   ============================================= */
.dash-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.dash-header h1 {
    font-size: clamp(1.125rem, 3vw, 1.75rem);
    font-weight: 700;
    color: #1a3d1f;
    margin: 0;
}

.dash-header-actions {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    flex-wrap: wrap;
    flex-shrink: 0;
}

.breadcrumb-nav {
    font-size: 0.78rem;
    color: #9ca3af;
    margin-bottom: 0.25rem;
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

.inventory-color-container {
    width: 100%;
    height: 130px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform .2s ease;
    position: absolute;
    top: 0;
    left: 0;
}

.inventory-color-text {
    color: white;
    font-weight: 700;
    font-size: clamp(0.875rem, 3vw, 1.25rem);
    text-shadow: 0 1px 2px rgba(0,0,0,.3);
    letter-spacing: 0.3px;
    text-align: center;
    padding: 0.75rem;
    word-wrap: break-word;
    line-height: 1.1;
    display: flex;
    align-items: center;
    justify-content: center;
    max-width: 100%;
    overflow: hidden;
}

.inventory-item-card:hover .inventory-color-container {
    transform: scale(1.03);
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

/* =============================================
   RESPONSIVE HEADER
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