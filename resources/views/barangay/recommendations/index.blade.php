@extends('admin.layouts.app')
@section('title', 'Beneficiary Recommendations')
@section('breadcrumb', 'Recommendations')

@section('content')
<div class="dash-header">
    <h1>Beneficiary Recommendations</h1>
    <a href="{{ route('barangay.recommendations.create') }}" class="btn-primary">+ Recommend Beneficiary</a>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($recommendations->isEmpty())
    <div class="section-card" style="text-align:center;padding:3rem;">
        <p style="color:#888;">No beneficiary recommendations found.</p>
        <a href="{{ route('barangay.recommendations.create') }}" class="btn-primary" style="margin-top:1rem;display:inline-block;">
            Recommend First Beneficiary
        </a>
    </div>
@else
    <div class="section-card">
        <table class="items-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recommendations as $recommendation)
                <tr>
                    <td>#{{ str_pad($recommendation->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $recommendation->full_name }}</td>
                    <td>{{ $recommendation->contact_number ?? 'N/A' }}</td>
                    <td>
                        <span class="priority-badge priority-{{ $recommendation->priority_level }}">
                            {{ ucfirst($recommendation->priority_level) }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $recommendation->status }}">
                            {{ ucfirst($recommendation->status) }}
                        </span>
                    </td>
                    <td>{{ $recommendation->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('barangay.recommendations.show', $recommendation->id) }}" class="btn-sm-primary">View</a>
                        @if($recommendation->status == 'pending')
                            <a href="{{ route('barangay.recommendations.edit', $recommendation->id) }}" class="btn-sm-secondary">Edit</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<style>
.priority-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
}
.priority-low {
    background-color: #d4edda;
    color: #155724;
}
.priority-medium {
    background-color: #fff3cd;
    color: #856404;
}
.priority-high {
    background-color: #f8d7da;
    color: #721c24;
}
.priority-critical {
    background-color: #dc3545;
    color: white;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
}
.status-pending {
    background-color: #fff3cd;
    color: #856404;
}
.status-approved {
    background-color: #d4edda;
    color: #155724;
}
.status-rejected {
    background-color: #f8d7da;
    color: #721c24;
}
.status-converted {
    background-color: #cce5ff;
    color: #004085;
}
</style>
@endsection
