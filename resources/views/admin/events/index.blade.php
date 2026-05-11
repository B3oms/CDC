@extends('admin.layouts.app')
@section('title', 'Relief Events Management')
@section('breadcrumb', '<i class="fas fa-hand-holding-heart"></i> Relief Management / Events')

@section('content')

<div class="page-header">
    <div class="page-title">
        <h1>Relief Events</h1>
        <p class="page-description">Manage and monitor all relief operations</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.events.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Create New Event
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3>All Relief Events</h3>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events ?? [])
                    <tr>
                        <td>
                            <a href="{{ route('admin.events.show', $event->id) }}" class="table-link">
                                {{ $event->name }}
                            </a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</td>
                        <td>{{ $event->municipality->name ?? 'N/A' }}</td>
                        <td>
                            <span class="relief-status-badge {{ strtolower($event->status) }}">
                                {{ $event->status }}
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if($event->status === 'Upcoming')
                                    <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-calendar-times" style="font-size: 2rem; color: #888780; margin-bottom: 0.5rem;"></i>
                                <p>No relief events found</p>
                                <a href="{{ route('admin.events.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Create First Event
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.table-link {
    color: #185fa5;
    text-decoration: none;
    font-weight: 500;
}

.table-link:hover {
    text-decoration: underline;
}

.table-actions {
    display: flex;
    gap: 0.5rem;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #888780;
}

.empty-state p {
    font-size: 1rem;
    margin: 0.5rem 0 0 0;
}

.empty-state .btn {
    margin-top: 1rem;
}
</style>
