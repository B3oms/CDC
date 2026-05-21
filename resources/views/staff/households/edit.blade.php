@extends('staff.layouts.app')
@section('title', 'Edit Household')
@section('breadcrumb', 'Edit Household')

@section('content')
<div class="dash-header">
    <div>
        <h1>Edit Household #{{ $id }}</h1>
    </div>
    <x-back-button href="{{ route('staff.households.show', $id) }}" label="Back" />
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

    <form method="POST" action="{{ route('staff.households.update', $id) }}">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group full-width">
                <label>Household Information</label>
                <p>Household edit form will be implemented here.</p>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Update Household</button>
            <a href="{{ route('staff.households.show', $id) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
