@extends('admin.layouts.app')
@section('title', 'Add Category')

@section('content')
<div class="dash-header">
    <h1>Add Category</h1>
    <a href="{{ route('admin.inventory.index') }}" class="btn-back">← Back</a>
</div>

<div class="form-card">
    @if($errors->any())
    <div class="alert-error">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.inventory.category.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    placeholder="e.g. Can Goods" required>
            </div>

            
            <div class="form-group full-width">
                <label>Description</label>
                <textarea name="description" rows="2"
                    placeholder="Short description of this category">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Add Category</button>
            <a href="{{ route('admin.inventory.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
/* Form Specific Responsive Styles */
@media (max-width: 1024px) {
    .dash-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .dash-header h1 {
        text-align: center;
        font-size: 1.5rem;
    }
    
    .btn-back {
        align-self: center;
        max-width: 150px;
    }
    
    .form-card {
        padding: 1.5rem;
        margin: 0 auto;
        max-width: 600px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-group label {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        font-size: 0.9rem;
        padding: 0.75rem;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }
    
    .form-actions .btn {
        width: 100%;
        padding: 12px 16px;
        font-size: 0.9rem;
    }
    
    .alert-error {
        padding: 0.75rem;
        font-size: 0.9rem;
    }
    
    .alert-error ul {
        margin: 0;
        padding-left: 1.5rem;
    }
}

@media (max-width: 768px) {
    .dash-header {
        padding: 1rem;
    }
    
    .dash-header h1 {
        font-size: 1.25rem;
    }
    
    .btn-back {
        padding: 10px 14px;
        font-size: 0.85rem;
    }
    
    .form-card {
        padding: 1rem;
        margin: 0;
        max-width: 100%;
    }
    
    .form-group label {
        font-size: 0.85rem;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        font-size: 0.85rem;
        padding: 0.75rem;
    }
    
    .form-actions {
        margin-top: 1rem;
    }
    
    .form-actions .btn {
        padding: 10px 14px;
        font-size: 0.85rem;
    }
    
    .alert-error {
        padding: 0.5rem;
        font-size: 0.8rem;
    }
}

@media (max-width: 480px) {
    .dash-header {
        padding: 0.75rem;
    }
    
    .dash-header h1 {
        font-size: 1.1rem;
    }
    
    .btn-back {
        padding: 8px 12px;
        font-size: 0.8rem;
    }
    
    .form-card {
        padding: 0.75rem;
    }
    
    .form-group label {
        font-size: 0.8rem;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        font-size: 0.8rem;
        padding: 0.6rem;
    }
    
    .form-actions {
        margin-top: 0.75rem;
    }
    
    .form-actions .btn {
        padding: 8px 12px;
        font-size: 0.8rem;
    }
    
    .alert-error {
        padding: 0.5rem;
        font-size: 0.75rem;
    }
    
    .alert-error ul {
        padding-left: 1rem;
    }
}

/* Landscape Mobile for Forms */
@media (max-width: 768px) and (orientation: landscape) {
    .dash-header {
        flex-direction: row;
        align-items: center;
        padding: 0.5rem 1rem;
    }
    
    .dash-header h1 {
        font-size: 1rem;
    }
    
    .btn-back {
        padding: 6px 10px;
        font-size: 0.75rem;
    }
    
    .form-card {
        padding: 0.75rem;
    }
    
    .form-grid {
        gap: 0.75rem;
    }
    
    .form-group {
        margin-bottom: 0.75rem;
    }
    
    .form-actions {
        margin-top: 0.75rem;
    }
}

/* Tablet Portrait for Forms */
@media (min-width: 768px) and (max-width: 1024px) and (orientation: portrait) {
    .form-card {
        max-width: 500px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

