@extends('admin.layouts.app')
@section('title', 'Inventory')
@section('breadcrumb', 'Inventory')

@section('content')
<div class="dash-header">
    <h1>Inventory</h1>
    <div class="dash-header-actions">
        <div class="pdf-export-dropdown" style="position:relative;display:inline-block;">
            <button onclick="togglePdfDropdown()" class="btn-export-pdf"
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
        <a href="{{ route('admin.inventory.category.create') }}" class="btn-primary">+ Add Category</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($categories->isEmpty())
<div class="section-card" style="text-align:center;padding:3rem;">
    <p style="color:#888;">No categories yet.</p>
    <a href="{{ route('admin.inventory.category.create') }}"
        class="btn-primary" style="margin-top:1rem;display:inline-block;">
        Add First Category
    </a>
</div>
@else
<div class="inventory-grid">
    @foreach($categories as $category)
    <div class="inventory-category-card">
        <a href="{{ route('admin.inventory.category.show', $category->id) }}" class="inventory-card-link">
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
@endsection

@push('styles')
<style>
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

.inventory-color-container {
    width: 100%;
    height: 120px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    border: 2px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.inventory-color-container:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.inventory-color-text {
    color: white;
    font-weight: 600;
    font-size: 24px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    letter-spacing: 1px;
}

.inventory-card-img {
    height: 120px;
    margin-bottom: 12px;
}

@media (max-width: 768px) {
    .dash-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }

    .dash-header h1 {
        text-align: center;
        font-size: 1.25rem;
    }

    .inventory-card-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
}

@media (max-width: 480px) {
    .dash-header h1 {
        font-size: 1.1rem;
    }

    .inventory-card-img {
        height: 80px;
    }

    .inventory-color-text {
        font-size: 18px;
    }
}
</style>
@push('scripts')
<script>
// PDF Export Functions
function togglePdfDropdown() {
    const dropdown = document.getElementById('pdfOptions');
    if (dropdown.style.display === 'none') {
        dropdown.style.display = 'block';
    } else {
        dropdown.style.display = 'none';
    }
}

function exportPdf() {
    const paperSize = document.getElementById('paperSize').value;
    const orientation = document.getElementById('orientation').value;
    const url = `{{ route('admin.inventory.pdf') }}`;
    const fullUrl = `${url}?paper_size=${paperSize}&orientation=${orientation}`;
    window.open(fullUrl, '_blank');
    document.getElementById('pdfOptions').style.display = 'none';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('pdfOptions');
    const button = event.target.closest('.pdf-export-dropdown');
    if (!button && dropdown && dropdown.style.display === 'block') {
        dropdown.style.display = 'none';
    }
});
</script>
@endpush