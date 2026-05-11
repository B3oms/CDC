@extends('admin.layouts.app')
@section('title', 'Add Calamity Event')

@section('content')
<div class="dash-header">
    <h1>Add Calamity Event</h1>
    <a href="{{ route('admin.dashboard') }}" class="btn-back">← Back</a>
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

    <form method="POST" action="{{ route('admin.calamity.store') }}">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label>Calamity Name</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Typhoon Carina" required>
            </div>

            <div class="form-group">
                <label>Type</label>
                <input type="text" name="type" value="{{ old('type') }}" placeholder="e.g. Typhoon, Flood, Earthquake" required>
            </div>


            <div class="form-group">
                <label>Date Occurred</label>
                <input type="date" name="date_occurred" value="{{ old('date_occurred') }}" required>
            </div>

            <div class="form-group full-width">
                <label>Description</label>
                <textarea name="description" rows="3" placeholder="Brief description of the calamity...">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="form-group full-width">
            <label>Select Partner Barangays</label>
            <p class="hint">Choose which barangays can access this calamity portal.</p>

            @foreach($municipalities as $municipality)
            <div class="municipality-group">
                <div class="municipality-header">
                    <input type="checkbox" class="select-all" data-municipality="{{ $municipality->id }}">
                    <strong>{{ $municipality->name }}</strong>
                </div>
                <div class="barangay-list">
                    @foreach($municipality->barangays as $barangay)
                    <label class="barangay-check">
                        <input type="checkbox"
                            name="barangay_ids[]"
                            value="{{ $barangay->id }}"
                            class="brgy-{{ $municipality->id }}"
                            {{ in_array($barangay->id, old('barangay_ids', [])) ? 'checked' : '' }}>
                        {{ $barangay->name }}
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Open Portal</button>
            <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.select-all').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const municipalityId = this.dataset.municipality;
        document.querySelectorAll('.brgy-' + municipalityId).forEach(brgy => {
            brgy.checked = this.checked;
        });
    });
});
</script>
@endpush