@extends('staff.layouts.app')
@section('title', 'Inventory')
@section('breadcrumb', 'Inventory')

@section('content')
<div class="dash-header">
    <h1>Inventory</h1>
    <div class="dash-header-actions">
        <div class="pdf-export-dropdown" style="position:relative;display:inline-block;">
            <button onclick="togglePdfDropdown(event)" class="btn-export-pdf"
               style="display: inline-flex !important; align-items: center !important; gap: 6px !important; padding: 8px 16px !important; background: #10b981 !important; color: white !important; text-decoration: none !important; border-radius: 6px !important; font-size: 13px !important; font-weight: 500 !important; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3) !important; letter-spacing: 0.5px !important; border:none !important; cursor:pointer !important;"
               onmouseover="this.style.background='#059669'"
               onmouseout="this.style.background='#10b981'">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
            <div id="pdfOptions" class="pdf-options" style="display:none;position:absolute;top:100%;right:0;background:white;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);padding:12px;min-width:200px;z-index:1001;">
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Paper Size</label>
                    <select id="paperSize" style="width:100%;padding:6px 8px;border:1px solid #d1d5db;border-radius:4px;font-size:13px;color:#374151;">
                        <option value="A4">A4</option>
                        <option value="Letter">Letter</option>
                        <option value="Legal">Legal</option>
                    </select>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Orientation</label>
                    <select id="orientation" style="width:100%;padding:6px 8px;border:1px solid #d1d5db;border-radius:4px;font-size:13px;color:#374151;">
                        <option value="portrait">Portrait</option>
                        <option value="landscape" selected>Landscape</option>
                    </select>
                </div>
                <button onclick="exportPdf()" style="width:100%;padding:8px;background:#10b981;color:white;border:none;border-radius:4px;font-size:13px;font-weight:500;cursor:pointer;transition:background 0.2s;"
                   onmouseover="this.style.background='#059669'"
                   onmouseout="this.style.background='#10b981'">
                    Export PDF
                </button>
            </div>
        </div>
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
   EXPORT BUTTON
   ============================================= */
.btn-export-pdf {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #10b981;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 500;
    transition: background .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 4px 6px -1px rgba(16,185,129,.3);
    letter-spacing: .4px;
    white-space: nowrap;
}

.btn-export-pdf:hover {
    background: #059669;
    transform: translateY(-1px);
    box-shadow: 0 8px 12px -2px rgba(16,185,129,.4);
    color: white;
    text-decoration: none;
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
}

.inventory-color-container {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform .2s ease, box-shadow .2s ease;
}

.inventory-color-text {
    color: white;
    font-weight: 700;
    font-size: 1.5rem;
    text-shadow: 0 1px 2px rgba(0,0,0,.3);
    letter-spacing: 1px;
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

    .btn-export-pdf,
    .btn-primary {
        flex: 1;
        justify-content: center;
        text-align: center;
    }
}
</style>
@push('scripts')
<script>
// PDF Export Functions
let dropdownOpenTime = 0;

function togglePdfDropdown(event) {
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }
    const dropdown = document.getElementById('pdfOptions');
    if (dropdown.style.display === 'none') {
        dropdown.style.display = 'block';
        dropdownOpenTime = Date.now();
    } else {
        dropdown.style.display = 'none';
    }
}

// Prevent dropdown from closing when clicking inside
document.getElementById('pdfOptions').addEventListener('click', function(event) {
    event.stopPropagation();
    event.preventDefault();
});

// Close dropdown when clicking outside (with delay to prevent immediate closing)
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('pdfOptions');
    const button = event.target.closest('.pdf-export-dropdown');
    const insideDropdown = event.target.closest('#pdfOptions');
    
    // Don't close if just opened (within 200ms)
    if (Date.now() - dropdownOpenTime < 200) {
        return;
    }
    
    if (!button && !insideDropdown && dropdown && dropdown.style.display === 'block') {
        dropdown.style.display = 'none';
    }
});

function exportPdf() {
    const paperSize = document.getElementById('paperSize').value;
    const orientation = document.getElementById('orientation').value;
    const url = `{{ route('staff.inventory.pdf') }}`;
    const fullUrl = `${url}?paper_size=${paperSize}&orientation=${orientation}`;
    window.open(fullUrl, '_blank');
    document.getElementById('pdfOptions').style.display = 'none';
}

// Removed click-outside listener to prevent dropdown from closing unexpectedly
</script>
@endpush