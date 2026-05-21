@extends('staff.layouts.app')
@section('title', 'Inventory')
@section('breadcrumb', 'Inventory')

@section('content')
<div class="dash-header">
    <h1>Inventory</h1>
    <div class="dash-header-actions">
        <x-pdf-export-dropdown export-onclick="exportPdf()" :landscape-default="true" />
        <a href="{{ route('staff.inventory.category.create') }}" class="btn-primary">+ Add Category</a>
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
            <form method="GET" action="{{ route('staff.inventory.category.edit', $category->id) }}" style="display:inline;">
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
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = `{{ route('staff.inventory.pdf') }}`;
    form.style.display = 'none';

    [['paper_size', document.getElementById('paperSize').value], ['orientation', document.getElementById('orientation').value]].forEach(([name, value]) => {
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