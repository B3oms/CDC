@extends('admin.layouts.app')
@section('title', 'Inventory')
@section('breadcrumb', 'Inventory')

@section('content')
<div class="dash-header">
    <h1>Inventory</h1>
    <div class="dash-header-actions">
        <form method="GET" action="{{ route('admin.inventory.index') }}" class="header-search-form">
            <div class="header-search-container">
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}" 
                       placeholder="Search items..." 
                       class="header-search-input"
                       autocomplete="off">
                <button type="submit" class="header-search-btn">
                    <i class="fas fa-search"></i>
                </button>
                @if($search)
                    <a href="{{ route('admin.inventory.index') }}" class="header-clear-btn">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
        <x-pdf-export-dropdown export-onclick="exportPdf()" :landscape-default="true" />
        <a href="{{ route('admin.inventory.category.create') }}" class="btn-primary">+ Add Category</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

{{-- Inventory Alerts Section --}}
@if($lowStockItems->count() > 0 || $expiringItems->count() > 0)
<div class="inventory-alerts-section" style="margin-bottom:2rem;">
    @if($lowStockItems->count() > 0)
    <div class="section-card inventory-alert-card" style="margin-bottom:1.5rem;">
        <h3 class="section-title" style="color:#dc3545;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-exclamation-triangle"></i> Low Stock Items ({{ $lowStockItems->count() }})
        </h3>
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockItems as $inventory)
                    <tr>
                        <td>
                            @if($inventory->item->subcategory_id)
                                <a href="{{ route('admin.inventory.subcategory', $inventory->item->subcategory_id) }}" class="link">
                                    {{ $inventory->item->name }}
                                </a>
                            @else
                                {{ $inventory->item->name }}
                            @endif
                        </td>
                        <td>
                            @if($inventory->item->subcategory)
                                {{ $inventory->item->subcategory->category->name ?? 'Unknown' }} > {{ $inventory->item->subcategory->name }}
                            @else
                                Unknown Category
                            @endif
                        </td>
                        <td style="color:#dc3545;font-weight:600;">{{ $inventory->quantity }}</td>
                        <td>{{ $inventory->item->unit }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    
    @if($expiringItems->count() > 0)
    <div class="section-card inventory-alert-card">
        <h3 class="section-title" style="color:#ffc107;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-clock"></i> Expiring Items ({{ $expiringItems->count() }})
        </h3>
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Expiration Date</th>
                        <th>Days Until Expiry</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expiringItems as $inventory)
                    <tr>
                        <td>
                            @if($inventory->item->subcategory_id)
                                <a href="{{ route('admin.inventory.subcategory', $inventory->item->subcategory_id) }}" class="link">
                                    {{ $inventory->item->name }}
                                </a>
                            @else
                                {{ $inventory->item->name }}
                            @endif
                        </td>
                        <td>
                            @if($inventory->item->subcategory)
                                {{ $inventory->item->subcategory->category->name ?? 'Unknown' }} > {{ $inventory->item->subcategory->name }}
                            @else
                                Unknown Category
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($inventory->expiration_date)->format('M d, Y') }}</td>
                        <td style="color:#ffc107;font-weight:600;">
                            @php
                                $expirationDate = \Carbon\Carbon::parse($inventory->expiration_date);
                                $daysUntil = (int) $expirationDate->diffInDays(\Carbon\Carbon::now(), false);
                                if ($daysUntil < 0) {
                                    $daysText = abs($daysUntil) . ' days ago (Expired)';
                                    $textColor = '#dc3545';
                                } elseif ($daysUntil == 0) {
                                    $daysText = 'Expires today';
                                    $textColor = '#ff6b35';
                                } else {
                                    $daysText = $daysUntil . ' days';
                                    $textColor = '#ffc107';
                                }
                            @endphp
                            <span style="color:{{ $textColor }};">{{ $daysText }}</span>
                        </td>
                        <td>{{ $inventory->quantity }} {{ $inventory->item->unit }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endif

@if($search)
{{-- Search Results --}}
<div class="search-results-section" style="margin-bottom:2rem;">
    <h3 class="section-title">Search Results for "{{ $search }}"</h3>
    @if($searchResults->count() > 0)
        <div class="search-results-grid">
            @foreach($searchResults as $item)
            <div class="search-result-item">
                <div class="item-header">
                    <h4>{{ $item->name }}</h4>
                    <div class="item-category">
                        @if($item->subcategory)
                            {{ $item->subcategory->category->name ?? 'Unknown' }} > {{ $item->subcategory->name }}
                        @else
                            No category assigned
                        @endif
                    </div>
                </div>
                <div class="item-actions">
                    @if($item->subcategory_id)
                        <a href="{{ route('admin.inventory.subcategory', $item->subcategory_id) }}" class="btn-sm-primary">
                            View Item
                        </a>
                    @endif
                    <a href="{{ route('admin.inventory.item.edit', $item->id) }}" class="btn-sm-secondary">
                        Edit
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="no-results" style="text-align:center;padding:2rem;">
            <p style="color:#888;">No items found matching "{{ $search }}"</p>
            <a href="{{ route('admin.inventory.index') }}" class="btn-secondary" style="margin-top:1rem;display:inline-block;">
                Clear Search
            </a>
        </div>
    @endif
</div>
@endif

@if($categories->isEmpty() && !$search)
<div class="section-card" style="text-align:center;padding:3rem;">
    <p style="color:#888;">No categories yet.</p>
    <a href="{{ route('admin.inventory.category.create') }}"
        class="btn-primary" style="margin-top:1rem;display:inline-block;">
        Add First Category
    </a>
</div>
@else
@if(!$search)
<div class="inventory-grid">
    @foreach($categories as $category)
    <div class="inventory-category-card">
        <a href="{{ route('admin.inventory.category.show', $category->id) }}" class="inventory-card-link">
            <div class="inventory-card-img">
            <div class="inventory-color-container" style="background-color: {{ $category->color ?? '#10B981' }};">
                <div class="inventory-color-text">
                    {{ strtoupper($category->name) }}
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
            <form method="GET" action="{{ route('admin.inventory.category.edit', $category->id) }}" style="display:inline;">
                <button type="submit" class="btn-sm-secondary">Edit</button>
            </form>
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
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.dash-header h1 {
    font-size: clamp(1.25rem, 3vw, 1.875rem);
    font-weight: 700;
    color: #1a3d1f;
    margin: 0;
}

.dash-header-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

/* =============================================
   INVENTORY GRID
   4 cols maximized → 3 → 2 → 1
   ============================================= */
.inventory-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);   /* maximized / large desktop */
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
    .dash-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    .dash-header-actions {
        flex-direction: column;
        width: 100%;
        gap: 0.5rem;
    }
    .header-search-form {
        margin-right: 0;
        width: 100%;
    }
    .header-search-input {
        width: 100%;
    }
}

/* =============================================
   HEADER SEARCH
   ============================================= */
.header-search-form {
    margin-right: 1rem;
}

.header-search-container {
    display: flex;
    align-items: center;
    position: relative;
}

.header-search-input {
    width: 250px;
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px 0 0 6px;
    font-size: 0.875rem;
    outline: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    background: #fff;
}

.header-search-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.header-search-btn {
    padding: 0.5rem 0.75rem;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 0 6px 6px 0;
    cursor: pointer;
    transition: background-color 0.2s ease;
    font-size: 0.875rem;
}

.header-search-btn:hover {
    background: #2563eb;
}

.header-clear-btn {
    margin-left: 0.25rem;
    padding: 0.5rem;
    background: #6b7280;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 0.75rem;
    text-decoration: none;
    transition: background-color 0.2s ease;
    display: flex;
    align-items: center;
}

.header-clear-btn:hover {
    background: #4b5563;
}

/* =============================================
   SEARCH RESULTS
   ============================================= */
.search-results-section {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.search-results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.search-result-item {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 1rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.search-result-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.item-header h4 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
    font-size: 1.1rem;
    font-weight: 600;
}

.item-category {
    color: #6b7280;
    font-size: 0.85rem;
    margin-bottom: 1rem;
}

.item-actions {
    display: flex;
    gap: 0.5rem;
}

.no-results {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
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
    margin: 0;
    padding: 0;
}

.inventory-color-container {
    width: 100%;
    height: 130px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform .2s ease, box-shadow .2s ease;
    position: absolute;
    top: 0;
    left: 0;
}

.inventory-color-text {
    color: white;
    font-weight: 700;
    font-size: clamp(0.875rem, 2.5vw, 1.25rem);
    text-shadow: 0 1px 2px rgba(0,0,0,.3);
    letter-spacing: 0.5px;
    text-align: center;
    padding: 0.5rem;
    word-wrap: break-word;
    line-height: 1.2;
    display: flex;
    align-items: center;
    justify-content: center;
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

/* On very small screens, stack action buttons */
@media (max-width: 360px) {
    .inventory-card-actions {
        flex-direction: column;
    }

    .btn-sm-secondary,
    .btn-sm-danger {
        width: 100%;
        justify-content: center;
        text-align: center;
    }
}

/* Stack header on mobile */
@media (max-width: 600px) {
    .dash-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .dash-header-actions {
        width: 100%;
    }

    .btn-pdf,
    .btn-primary {
        flex: 1;
        justify-content: center;
        text-align: center;
    }
}
</style>
@push('scripts')
<script>
function exportPdf() {
    const paperSize = document.getElementById('paperSize').value;
    const orientation = document.getElementById('orientation').value;
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = `{{ route('admin.inventory.pdf') }}`;
    form.style.display = 'none';

    [['paper_size', paperSize], ['orientation', orientation]].forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    closePdfDropdown('pdfOptions');
}
</script>
@endpush