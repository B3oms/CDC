@extends('staff.layouts.app')
@section('title', 'Add Household')
@section('breadcrumb', 'Add Household')

@section('content')
<div class="dash-header">
    <div>
        <h1>Add Household</h1>
    </div>
    <x-back-button href="{{ route('staff.households.index') }}" label="Back" />
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

    <form method="POST" action="{{ route('staff.households.store') }}">
        @csrf
        <div class="form-grid">
            <div class="form-group full-width">
                <label>Household Information</label>
                <p>Household creation form will be implemented here.</p>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Add Household</button>
            <a href="{{ route('staff.households.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
