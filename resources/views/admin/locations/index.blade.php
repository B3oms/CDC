@extends('admin.layouts.app')
@section('title', 'Location Management')

@section('content')
<div class="locations-header">
    <div class="locations-title">
        <h1>Location Management</h1>
        <p class="locations-subtitle">Manage and monitor location requests</p>
    </div>
    <div class="locations-actions">
        <a href="{{ route('admin.locations.create') }}" class="btn-add-location">
            <i class="fas fa-plus"></i> Add Location
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
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

{{-- Enhanced Statistics Row --}}
<div class="stats-row" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-num">{{ $pendingRequests ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-clock"></i> Pending Requests
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $approvedRequests ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-check-circle"></i> Approved Requests
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $rejectedRequests ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-times-circle"></i> Rejected Requests
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $totalLocations ?? 0 }}</div>
        <div class="stat-label">
            <i class="fas fa-map-marker-alt"></i> Total Locations
        </div>
    </div>
</div>

@if($locationRequests->isEmpty())

    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-map-marked-alt"></i>
        </div>
        <h3>No location requests yet</h3>
        <p>Start by adding your first location request</p>
        <a href="{{ route('admin.locations.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Add First Location
        </a>
    </div>

@else

    {{-- Pending Requests Table --}}
    @php $pendingCount = $locationRequests->where('status', 'pending')->count(); @endphp

    @if($pendingCount > 0)
    <div class="locations-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-clock"></i>
                Pending Requests
                <span class="badge-count">{{ $pendingCount }}</span>
            </h2>
        </div>
        <div class="locations-table">
            <div class="table-header">
                <div class="table-cell">Location Name</div>
                <div class="table-cell">Type</div>
                <div class="table-cell">Submitted By</div>
                <div class="table-cell">Date</div>
                <div class="table-cell">Status</div>
                <div class="table-cell">Actions</div>
            </div>
            @foreach($locationRequests->where('status', 'pending') as $request)
            <div class="table-row">
                <div class="table-cell">
                    <div class="location-name">{{ $request->name }}</div>
                </div>
                <div class="table-cell">
                    <span class="location-type">{{ ucfirst($request->type) }}</span>
                </div>
                <div class="table-cell">
                    <div class="submitter-info">
                        {{ $request->requested_by_firstname }} {{ $request->requested_by_lastname }}
                    </div>
                </div>
                <div class="table-cell">
                    <div class="date-info">{{ date('M d, Y', strtotime($request->created_at)) }}</div>
                </div>
                <div class="table-cell">
                    <span class="status-badge pending">
                        <i class="fas fa-clock"></i> Pending
                    </span>
                </div>
                <div class="table-cell">
                    <div class="action-buttons">
                        <form method="POST" action="{{ route('admin.locations.approve', $request->id) }}" class="action-form">
                            @csrf
                            <button type="submit" class="btn-approve">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.locations.reject', $request->id) }}" class="action-form">
                            @csrf
                            <input type="hidden" name="rejection_reason" value="Rejected by admin">
                            <button type="submit" class="btn-reject">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                        <a href="{{ route('admin.locations.show', $request->id) }}" class="btn-view">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- All System Locations --}}
    @if($totalLocations > 0)
        @php
            $municipalities = $allLocations->where('type', 'municipality')->sortBy('name');
            $barangays      = $allLocations->where('type', 'barangay');

            $barangayGroups = [];
            foreach ($barangays as $barangay) {
                $municipalityName = $barangay->municipality_name ?? 'Uncategorized';
                $barangayGroups[$municipalityName][] = $barangay;
            }
            ksort($barangayGroups);

            $orphanedBarangays = $barangays->filter(function ($barangay) use ($municipalities) {
                return !$municipalities->contains('name', $barangay->municipality_name);
            });
        @endphp

        <div class="locations-organized">
            <div class="locations-tabs">
                <button class="tab-btn active" onclick="showLocationTab('municipalities', event)">
                    <i class="fas fa-city"></i> Municipalities ({{ $municipalities->count() }})
                </button>
                <button class="tab-btn" onclick="showLocationTab('hierarchy', event)">
                    <i class="fas fa-sitemap"></i> Hierarchy View
                </button>
            </div>

            {{-- Municipalities Tab --}}
            <div id="municipalities-tab" class="tab-content active">
                <div class="locations-grid">
                    @foreach($municipalities as $municipality)
                    <div class="location-card municipality-card">
                        <div class="location-card-header">
                            <div class="location-info">
                                <h3 class="location-name">{{ $municipality->name }}</h3>
                                <div class="location-meta">
                                    <span class="location-type-badge municipality">Municipality</span>
                                    <span class="location-province">{{ $municipality->province }}</span>
                                </div>
                            </div>
                            <div class="location-stats">
                                <span class="stat-badge">
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ $barangays->where('municipality_name', $municipality->name)->count() }} barangays
                                </span>
                            </div>
                        </div>
                        <div class="location-card-body">
                            <div class="location-details">
                                <div class="detail-item">
                                    <i class="fas fa-globe"></i>
                                    <span>{{ $municipality->province }}</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ $barangays->where('municipality_name', $municipality->name)->count() }} barangays</span>
                                </div>
                            </div>
                        </div>
                        <div class="location-card-actions">
                            <form method="POST" action="{{ route('admin.locations.destroy', $municipality->id) }}" class="action-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" onclick="return confirm('Delete this municipality?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Hierarchy Tab --}}
            <div id="hierarchy-tab" class="tab-content">
                <div class="hierarchy-container">

                    @foreach($municipalities as $municipality)
                    <div class="hierarchy-item municipality-hierarchy">
                        <div class="hierarchy-header" onclick="toggleHierarchy('muni-{{ $municipality->id }}')">
                            <div class="hierarchy-info">
                                <i class="fas fa-chevron-right hierarchy-toggle"></i>
                                <i class="fas fa-city"></i>
                                <span class="hierarchy-name">{{ $municipality->name }}</span>
                                <span class="hierarchy-type">Municipality</span>
                            </div>
                            <span class="hierarchy-count">
                                {{ $barangays->where('municipality_name', $municipality->name)->count() }} barangays
                            </span>
                        </div>
                        <div id="muni-{{ $municipality->id }}" class="hierarchy-children">
                            @foreach($barangays->where('municipality_name', $municipality->name)->sortBy('name') as $barangay)
                            <div class="hierarchy-item barangay-hierarchy">
                                <div class="hierarchy-header">
                                    <div class="hierarchy-info">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span class="hierarchy-name">{{ $barangay->name }}</span>
                                        <span class="hierarchy-type">Barangay</span>
                                    </div>
                                                                    </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    {{-- Orphaned barangays --}}
                    @if($orphanedBarangays->count() > 0)
                    <div class="hierarchy-item orphaned-hierarchy">
                        <div class="hierarchy-header" onclick="toggleHierarchy('orphaned')">
                            <div class="hierarchy-info">
                                <i class="fas fa-chevron-right hierarchy-toggle"></i>
                                <i class="fas fa-exclamation-triangle"></i>
                                <span class="hierarchy-name">Uncategorized Barangays</span>
                                <span class="hierarchy-type">Orphaned</span>
                            </div>
                            <span class="hierarchy-count">{{ $orphanedBarangays->count() }} barangays</span>
                        </div>
                        <div id="orphaned" class="hierarchy-children">
                            @foreach($orphanedBarangays as $barangay)
                            <div class="hierarchy-item barangay-hierarchy">
                                <div class="hierarchy-header">
                                    <div class="hierarchy-info">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span class="hierarchy-name">{{ $barangay->name }}</span>
                                        <span class="hierarchy-type">Barangay</span>
                                    </div>
                                                                    </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

    @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <h3>No locations in the system yet</h3>
            <p>Approve pending requests to add locations to the system.</p>
        </div>
    @endif

@endif

@push('styles')
<style>
.locations-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding: 1.5rem 0;
}

.locations-title h1 {
    font-size: 1.875rem;
    font-weight: 700;
    color: #1a3d1f;
    margin: 0 0 0.5rem 0;
}

.locations-subtitle {
    color: #6b7280;
    font-size: 0.875rem;
    margin: 0;
}

.locations-actions {
    display: flex;
    gap: 0.75rem;
}

.btn-filter {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: white;
    color: #374151;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-filter:hover {
    background: #f9fafb;
    border-color: #9ca3af;
}

.btn-add-location {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border: none;
    border-radius: 8px;
    background: #1a3d1f;
    color: white;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-add-location:hover {
    background: #2d4f33;
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.stat-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.stat-icon.pending  { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-icon.approved { background: linear-gradient(135deg, #10b981, #059669); }
.stat-icon.rejected { background: linear-gradient(135deg, #ef4444, #dc2626); }
.stat-icon.total    { background: linear-gradient(135deg, #3b82f6, #2563eb); }

.stat-number {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-success {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.alert-error {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.empty-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 1.5rem;
    background: #f3f4f6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #6b7280;
}

.empty-state h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #374151;
    margin: 0 0 0.5rem 0;
}

.empty-state p {
    color: #6b7280;
    margin: 0 0 2rem 0;
}

/* Pending table */
.locations-section { margin-bottom: 2rem; }

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-title i { color: #1a3d1f; }

.badge-count {
    background: #1a3d1f;
    color: white;
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    margin-left: 0.5rem;
}

.locations-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.table-header {
    display: grid;
    grid-template-columns: 2fr 1fr 1.5fr 1fr 1fr 1.5fr;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem 1.5rem;
    font-weight: 600;
    font-size: 0.875rem;
    color: #374151;
}

.table-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1.5fr 1fr 1fr 1.5fr;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    transition: background-color 0.2s ease;
}

.table-row:last-child { border-bottom: none; }
.table-row:hover { background: #f9fafb; }

.table-cell {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
}

.location-name { font-weight: 500; color: #1f2937; }

.location-type {
    background: #f3f4f6;
    color: #374151;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
}

.submitter-info, .date-info { color: #6b7280; }

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.pending  { background: #fbbf24 !important; color: #92400e !important; }
.status-badge.approved { background: #34d399 !important; color: #064e3b !important; }
.status-badge.rejected { background: #fee2e2 !important; color: #dc2626 !important; }

.action-buttons { display: flex; gap: 0.5rem; }
.action-form { display: inline; }

.btn-approve,
.btn-reject,
.btn-view {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    font-size: 0.875rem;
}

.btn-approve { background: #10b981; color: white; }
.btn-approve:hover { background: #059669; }
.btn-reject  { background: #ef4444; color: white; }
.btn-reject:hover  { background: #dc2626; }
.btn-view    { background: #f3f4f6; color: #6b7280; }
.btn-view:hover    { background: #e5e7eb; }

.btn-delete {
    background: #ef4444;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
}

.btn-delete:hover { background: #dc2626; }

.btn-view-small {
    width: 28px;
    height: 28px;
    border-radius: 4px;
    background: #f3f4f6;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 0.75rem;
    transition: all 0.2s ease;
}

.btn-view-small:hover { background: #e5e7eb; }

/* Organized locations */
.locations-organized {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.locations-tabs {
    display: flex;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}

.tab-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    border: none;
    background: transparent;
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border-bottom: 2px solid transparent;
}

.tab-btn:hover { color: #1f2937; background: rgba(26, 61, 31, 0.05); }
.tab-btn.active { color: #1a3d1f; background: white; border-bottom-color: #1a3d1f; }

.tab-content { display: none; padding: 1.5rem; }
.tab-content.active { display: block; }

.locations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.location-card {
    background: white;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    transition: all 0.3s ease;
}

.location-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.location-card-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.location-card-header .location-name {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
}

.location-meta {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}

.location-type-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.location-type-badge.municipality { background: #dbeafe; color: #1e40af; }

.location-province { color: #6b7280; font-size: 0.875rem; }

.stat-badge {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    background: #f3f4f6;
    color: #6b7280;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.location-card-body { padding: 1rem 1.5rem; }

.location-details { display: flex; flex-direction: column; gap: 0.5rem; }

.detail-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
    font-size: 0.875rem;
}

.detail-item i { color: #1a3d1f; width: 16px; }

.location-card-actions {
    padding: 1rem 1.5rem;
    border-top: 1px solid #f3f4f6;
    display: flex;
    gap: 0.75rem;
}

/* Hierarchy */
.hierarchy-container { display: flex; flex-direction: column; gap: 0.5rem; }

.hierarchy-item {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
}

.hierarchy-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.hierarchy-header:hover { background: #f9fafb; }

.hierarchy-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.hierarchy-toggle {
    color: #9ca3af;
    transition: transform 0.2s ease;
}

.hierarchy-toggle.expanded { transform: rotate(90deg); }

.hierarchy-name { font-weight: 500; color: #1f2937; }
.hierarchy-type { color: #6b7280; font-size: 0.875rem; }
.hierarchy-count { color: #6b7280; font-size: 0.875rem; }

.hierarchy-children {
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
    display: none;
}

.hierarchy-children.expanded { display: block; }

.barangay-hierarchy {
    border: none;
    border-radius: 0;
    margin: 0;
}

.barangay-hierarchy .hierarchy-header {
    padding: 0.75rem 1.5rem 0.75rem 3rem;
    background: transparent;
}

.barangay-hierarchy .hierarchy-header:hover {
    background: rgba(26, 61, 31, 0.05);
}

.hierarchy-actions { display: flex; gap: 0.5rem; }

.orphaned-hierarchy {
    border-color: #f59e0b;
    background: #fffbeb;
}

@media (max-width: 768px) {
    .locations-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }

    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }

    .table-header { display: none; }

    .table-row {
        grid-template-columns: 1fr;
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .table-cell {
        padding: 0.25rem 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .table-cell:last-child { border-bottom: none; }

    .table-cell::before {
        content: attr(data-label);
        font-weight: 600;
        color: #374151;
        margin-right: 0.5rem;
        min-width: 100px;
        display: inline-block;
    }

    .action-buttons {
        justify-content: flex-start;
        padding-top: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function showLocationTab(tabName, event) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(tabName + '-tab').classList.add('active');
    event.currentTarget.classList.add('active');
}

function toggleHierarchy(id) {
    const el     = document.getElementById(id);
    const toggle = event.currentTarget.querySelector('.hierarchy-toggle');
    el.classList.toggle('expanded');
    toggle.classList.toggle('expanded');
}

const filterBtn = document.querySelector('.btn-filter');
if (filterBtn) {
    filterBtn.addEventListener('click', function () {
        alert('Filter functionality coming soon!');
    });
}
</script>
@endpush

@endsection