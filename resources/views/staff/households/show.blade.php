@extends('staff.layouts.app')
@section('title', 'Household Details')
@section('breadcrumb', 'Household Details')

@section('content')
<div class="dash-header">
    <div>
        <h1>Household #{{ $id }}</h1>
        <p class="sub">Household details and information</p>
    </div>
    <div>
        <a href="{{ route('staff.households.edit', $id) }}" class="btn-secondary">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('staff.households.index') }}" class="btn-back">← Back</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="section-card">
    <h3>Household Information</h3>
    <p>Household details will be displayed here.</p>
</div>
@endsection
