@extends('staff.layouts.app')
@section('title', 'Households')
@section('breadcrumb', 'Households')

@section('content')
<div class="dash-header">
    <div>
        <h1>Households</h1>
        <p class="sub">Manage household information and records</p>
    </div>
    <div>
        <a href="{{ route('staff.households.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Add Household
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="section-card">
    <h3>Household Management</h3>
    <p>Household management functionality will be implemented here.</p>
    <p>This section will allow you to:</p>
    <ul>
        <li>Add and manage household records</li>
        <li>Track household members</li>
        <li>Monitor household status during relief operations</li>
        <li>Generate household reports</li>
    </ul>
</div>
@endsection
