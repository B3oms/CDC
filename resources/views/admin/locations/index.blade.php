@extends('admin.layouts.app')
@section('title', 'Location Management')

@section('content')

<div class="dash-header">
    <h1>Location Management</h1>
    <div class="dash-header-actions">
        <a href="{{ route('admin.locations.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Add Location
        </a>
    </div>
</div>

{{-- ===================== ALERTS ===================== --}}
@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:1.5rem;">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error" style="margin-bottom:1.5rem;">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<div class="stats-row" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-num">{{ $pendingRequests ?? 0 }}</div>
        <div class="stat-label"><i class="fas fa-clock"></i> Pending Requests</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $approvedRequests ?? 0 }}</div>
        <div class="stat-label"><i class="fas fa-check-circle"></i> Approved Requests</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $rejectedRequests ?? 0 }}</div>
        <div class="stat-label"><i class="fas fa-times-circle"></i> Rejected Requests</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $totalLocations ?? 0 }}</div>
        <div class="stat-label"><i class="fas fa-map-marker-alt"></i> Total Locations</div>
    </div>
</div>

@if($locationRequests->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-map-marked-alt"></i></div>
        <h3>No location requests yet</h3>
        <p>Start by adding your first location request</p>
        <a href="{{ route('admin.locations.create') }}" class="btn-add-location">
            <i class="fas fa-plus"></i> Add First Location
        </a>
    </div>
@else

    {{-- ===================== PENDING REQUESTS ===================== --}}
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

        {{-- Desktop table --}}
        <div class="table-wrapper">
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
                <div class="table-cell" data-label="Location">
                    <span class="location-name">{{ $request->name }}</span>
                </div>
                <div class="table-cell" data-label="Type">
                    <span class="location-type">{{ ucfirst($request->type) }}</span>
                </div>
                <div class="table-cell" data-label="Submitted By">
                    <span class="submitter-info">{{ $request->requested_by_firstname }} {{ $request->requested_by_lastname }}</span>
                </div>
                <div class="table-cell" data-label="Date">
                    <span class="date-info">{{ date('M d, Y', strtotime($request->created_at)) }}</span>
                </div>
                <div class="table-cell" data-label="Status">
                    <span class="status-badge pending">
                        <i class="fas fa-clock"></i> Pending
                    </span>
                </div>
                <div class="table-cell" data-label="Actions">
                    <div class="action-buttons">
                        <form method="POST" action="{{ route('admin.locations.approve', $request->id) }}" style="display:contents">
                            @csrf
                            <button type="submit" class="btn-action btn-approve" title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.locations.reject', $request->id) }}" style="display:contents">
                            @csrf
                            <input type="hidden" name="rejection_reason" value="Rejected by admin">
                            <button type="submit" class="btn-action btn-reject" title="Reject">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                        <a href="{{ route('admin.locations.show', $request->id) }}" class="btn-action btn-view" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>{{-- /.locations-table --}}
        </div>{{-- /.table-wrapper --}}
    </div>{{-- /.locations-section --}}
    @endif

    {{-- ===================== ALL SYSTEM LOCATIONS ===================== --}}
    @if($totalLocations > 0)
        @php
            $municipalities    = $allLocations->where('type', 'municipality')->sortBy('name');
            $barangays         = $allLocations->where('type', 'barangay');
            $orphanedBarangays = $barangays->filter(fn($b) => !$municipalities->contains('name', $b->municipality_name));
        @endphp

        <div class="locations-organized">
            <div class="locations-tabs">
                <button class="tab-btn active" onclick="showLocationTab('municipalities', event)">
                    <i class="fas fa-city"></i>
                    <span>Municipalities ({{ $municipalities->count() }})</span>
                </button>
                <button class="tab-btn" onclick="showLocationTab('hierarchy', event)">
                    <i class="fas fa-sitemap"></i>
                    <span>Hierarchy View</span>
                </button>
            </div>

            {{-- Municipalities Tab --}}
            <div id="municipalities-tab" class="tab-content active">
                <div class="locations-grid">
                    @foreach($municipalities as $municipality)
                    <div class="location-card municipality-card">
                        <div class="location-card-header">
                            <div class="location-info">
                                <h3 class="location-card-name">{{ $municipality->name }}</h3>
                                <div class="location-meta">
                                    <span class="location-type-badge municipality">Municipality</span>
                                    <span class="location-province">{{ $municipality->province }}</span>
                                </div>
                            </div>
                            <span class="stat-badge">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $barangays->where('municipality_name', $municipality->name)->count() }}
                            </span>
                        </div>
                        <div class="location-card-body">
                            <div class="detail-item"><i class="fas fa-globe"></i><span>{{ $municipality->province }}</span></div>
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $barangays->where('municipality_name', $municipality->name)->count() }} barangays</span>
                            </div>
                        </div>
                        <div class="location-card-actions">
                            <form method="POST" action="{{ route('admin.locations.destroy', $municipality->id) }}" style="display:contents">
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
                            <span class="hierarchy-count">{{ $barangays->where('municipality_name', $municipality->name)->count() }} barangays</span>
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
            <div class="empty-icon"><i class="fas fa-map-marked-alt"></i></div>
            <h3>No locations in the system yet</h3>
            <p>Approve pending requests to add locations to the system.</p>
        </div>
    @endif

@endif

@push('styles')
<style>
/* =============================================
   CSS VARIABLES
   ============================================= */
:root {
    --clr-primary:      #1a3d1f;
    --clr-primary-hover:#2d6a35;
    --clr-white:        #ffffff;
    --clr-gray-50:      #f9fafb;
    --clr-gray-100:     #f3f4f6;
    --clr-gray-200:     #e5e7eb;
    --clr-gray-400:     #9ca3af;
    --clr-gray-500:     #6b7280;
    --clr-gray-700:     #374151;
    --clr-gray-900:     #111827;
    --clr-green-bg:     #d1fae5;
    --clr-green-text:   #059669;
    --clr-red-bg:       #fee2e2;
    --clr-red-text:     #dc2626;
    --clr-yellow-bg:    #fef3c7;
    --clr-yellow-text:  #d97706;

    --radius-sm:  6px;
    --radius-md:  8px;
    --radius-lg:  12px;

    --shadow-sm: 0 1px 3px rgba(0,0,0,.08);
    --shadow-md: 0 4px 12px rgba(0,0,0,.12);
}

/* =============================================
   PAGE HEADER  — the main fix
   ============================================= */
.page-header {
    background: var(--clr-white);
    border-radius: var(--radius-lg);
    border: 1px solid var(--clr-gray-200);
    box-shadow: var(--shadow-sm);
    margin-bottom: 1.5rem;
    padding: 1.25rem 1.5rem;
}

.page-header-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.page-header-title {
    font-size: clamp(1.25rem, 3.5vw, 1.875rem);
    font-weight: 700;
    color: var(--clr-primary);
    margin: 0 0 0.25rem;
    line-height: 1.2;
}

.page-header-subtitle {
    color: var(--clr-gray-500);
    font-size: 0.875rem;
    margin: 0;
}

.page-header-actions {
    flex-shrink: 0;
}

/* =============================================
   ALERTS
   ============================================= */
.alert {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1.25rem;
    border-radius: var(--radius-md);
    margin-bottom: 1.25rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.alert-success { background: var(--clr-green-bg); color: var(--clr-green-text); border: 1px solid #bbf7d0; }
.alert-error   { background: var(--clr-red-bg);   color: var(--clr-red-text);   border: 1px solid #fecaca; }

/* =============================================
   STATS ROW
   ============================================= */
.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(100%, 180px), 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: var(--clr-white);
    border-radius: var(--radius-lg);
    border: 1px solid var(--clr-gray-200);
    box-shadow: var(--shadow-sm);
    padding: 1.25rem 1.5rem;
    transition: box-shadow .2s;
}

.stat-card:hover { box-shadow: var(--shadow-md); }

.stat-num {
    font-size: clamp(1.5rem, 4vw, 2rem);
    font-weight: 700;
    color: var(--clr-primary);
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--clr-gray-500);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: .4px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
    width: 100%;
}

/* =============================================
   BUTTONS
   ============================================= */
.btn-add-location {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border-radius: var(--radius-md);
    background: var(--clr-primary);
    color: var(--clr-white);
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: background .2s, transform .15s;
    white-space: nowrap;
    -webkit-tap-highlight-color: transparent;
}

.btn-add-location:hover  { background: var(--clr-primary-hover); }
.btn-add-location:active { transform: scale(.97); }

.btn-action {
    width: 34px;
    height: 34px;
    border-radius: var(--radius-sm);
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.8rem;
    text-decoration: none;
    transition: opacity .2s, transform .15s;
    -webkit-tap-highlight-color: transparent;
    flex-shrink: 0;
}

.btn-action:active { transform: scale(.93); }
.btn-approve { background: #10b981; color: var(--clr-white); }
.btn-approve:hover { opacity: .85; }
.btn-reject  { background: #ef4444; color: var(--clr-white); }
.btn-reject:hover  { opacity: .85; }
.btn-view    { background: var(--clr-gray-100); color: var(--clr-gray-500); }
.btn-view:hover    { background: var(--clr-gray-200); }

.btn-delete {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: #ef4444;
    color: var(--clr-white);
    padding: 0.5rem 1rem;
    border-radius: var(--radius-sm);
    border: none;
    cursor: pointer;
    font-size: 0.875rem;
    transition: opacity .2s;
}

.btn-delete:hover { opacity: .85; }

/* =============================================
   SECTION HEADER
   ============================================= */
.locations-section { margin-bottom: 2rem; }

.section-header { margin-bottom: 1rem; }

.section-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--clr-gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-title i { color: var(--clr-primary); }

.badge-count {
    background: var(--clr-primary);
    color: var(--clr-white);
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
}

/* =============================================
   TABLE  — horizontally scrollable on all screens
   ============================================= */
.table-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--clr-gray-200);
}

.locations-table {
    background: var(--clr-white);
    min-width: 640px; /* prevents columns from squishing */
    width: 100%;
}

/* Desktop header */
.table-header {
    display: grid;
    grid-template-columns: 2fr 1fr 1.5fr 1fr 1fr 1.2fr;
    background: var(--clr-gray-50);
    border-bottom: 1px solid var(--clr-gray-200);
    padding: 0.75rem 1.25rem;
    font-weight: 600;
    font-size: 0.8rem;
    color: var(--clr-gray-700);
    text-transform: uppercase;
    letter-spacing: .4px;
}

/* Desktop row */
.table-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1.5fr 1fr 1fr 1.2fr;
    padding: 0.875rem 1.25rem;
    border-bottom: 1px solid var(--clr-gray-100);
    align-items: center;
    transition: background .15s;
}

.table-row:last-child { border-bottom: none; }
.table-row:hover { background: var(--clr-gray-50); }

.table-cell {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
}

.location-name  { font-weight: 500; color: var(--clr-gray-900); }
.submitter-info,
.date-info      { color: var(--clr-gray-500); }

.location-type {
    background: var(--clr-gray-100);
    color: var(--clr-gray-700);
    padding: 0.2rem 0.6rem;
    border-radius: 5px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.65rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    white-space: nowrap;
}

.status-badge.pending  { background: #fbbf24; color: #92400e; }
.status-badge.approved { background: #34d399; color: #064e3b; }
.status-badge.rejected { background: var(--clr-red-bg); color: var(--clr-red-text); }

.action-buttons {
    display: flex;
    gap: 0.4rem;
    align-items: center;
}

/* =============================================
   EMPTY STATE
   ============================================= */
.empty-state {
    text-align: center;
    padding: 3.5rem 1.5rem;
    background: var(--clr-white);
    border-radius: var(--radius-lg);
    border: 1px solid var(--clr-gray-200);
}

.empty-icon {
    width: 60px; height: 60px;
    margin: 0 auto 1.25rem;
    background: var(--clr-gray-100);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
    color: var(--clr-gray-400);
}

.empty-state h3 { font-size: 1.125rem; font-weight: 600; color: var(--clr-gray-700); margin: 0 0 .5rem; }
.empty-state p  { color: var(--clr-gray-500); margin: 0 0 1.5rem; }

/* =============================================
   LOCATIONS ORGANIZED (tabs + cards)
   ============================================= */
.locations-organized {
    background: var(--clr-white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--clr-gray-200);
}

.locations-tabs {
    display: flex;
    background: var(--clr-gray-50);
    border-bottom: 1px solid var(--clr-gray-200);
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.tab-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.25rem;
    border: none;
    background: transparent;
    color: var(--clr-gray-500);
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
    border-bottom: 2px solid transparent;
    transition: color .2s, background .2s;
    -webkit-tap-highlight-color: transparent;
}

.tab-btn:hover  { color: var(--clr-gray-900); background: rgba(26,61,31,.04); }
.tab-btn.active { color: var(--clr-primary); background: var(--clr-white); border-bottom-color: var(--clr-primary); }

.tab-content        { display: none; padding: 1.25rem; }
.tab-content.active { display: block; }

.locations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(min(100%, 280px), 1fr));
    gap: 1rem;
}

.location-card {
    background: var(--clr-white);
    border-radius: var(--radius-md);
    border: 1px solid var(--clr-gray-200);
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
}

.location-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }

.location-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--clr-gray-100);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.75rem;
}

.location-card-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--clr-gray-900);
    margin: 0 0 0.4rem;
}

.location-meta { display: flex; flex-wrap: wrap; gap: 0.4rem; align-items: center; }

.location-type-badge {
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 500;
}

.location-type-badge.municipality { background: #dbeafe; color: #1e40af; }

.location-province { color: var(--clr-gray-500); font-size: 0.8rem; }

.stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    background: var(--clr-gray-100);
    color: var(--clr-gray-500);
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
    flex-shrink: 0;
}

.location-card-body { padding: 0.875rem 1.25rem; display: flex; flex-direction: column; gap: 0.4rem; }

.detail-item { display: flex; align-items: center; gap: 0.5rem; color: var(--clr-gray-500); font-size: 0.85rem; }
.detail-item i { color: var(--clr-primary); width: 14px; flex-shrink: 0; }

.location-card-actions { padding: 0.875rem 1.25rem; border-top: 1px solid var(--clr-gray-100); }

/* =============================================
   HIERARCHY
   ============================================= */
.hierarchy-container { display: flex; flex-direction: column; gap: 0.5rem; }

.hierarchy-item {
    background: var(--clr-white);
    border: 1px solid var(--clr-gray-200);
    border-radius: var(--radius-md);
    overflow: hidden;
}

.hierarchy-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.875rem 1.25rem;
    cursor: pointer;
    transition: background .15s;
    gap: 0.5rem;
}

.hierarchy-header:hover { background: var(--clr-gray-50); }

.hierarchy-info { display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap; }

.hierarchy-toggle { color: var(--clr-gray-400); transition: transform .2s; flex-shrink: 0; }
.hierarchy-toggle.expanded { transform: rotate(90deg); }

.hierarchy-name  { font-weight: 500; color: var(--clr-gray-900); }
.hierarchy-type  { color: var(--clr-gray-400); font-size: 0.8rem; }
.hierarchy-count { color: var(--clr-gray-500); font-size: 0.8rem; white-space: nowrap; }

.hierarchy-children { background: var(--clr-gray-50); border-top: 1px solid var(--clr-gray-200); display: none; }
.hierarchy-children.expanded { display: block; }

.barangay-hierarchy { border: none; border-radius: 0; }
.barangay-hierarchy .hierarchy-header { padding-left: 2.5rem; }
.barangay-hierarchy .hierarchy-header:hover { background: rgba(26,61,31,.05); }

.orphaned-hierarchy { border-color: #f59e0b; background: #fffbeb; }

/* =============================================
   RESPONSIVE
   ============================================= */

/* Tablet — keep 2-col stats */
@media (max-width: 768px) {
    /* Header stacks */
    .page-header-inner {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.875rem;
    }

    .btn-add-location { width: 100%; justify-content: center; }

    /* Stats: 2 per row */
    .stats-row { grid-template-columns: 1fr 1fr; }

    /* Table stays as-is; wrapper handles scroll */
}

/* Phone — 1-col stats */
@media (max-width: 480px) {
    .stats-row { grid-template-columns: 1fr 1fr; }
    .stat-card { padding: 1rem; }

    .locations-table { border-radius: var(--radius-md); }

    .hierarchy-header { padding: 0.75rem 1rem; }
    .barangay-hierarchy .hierarchy-header { padding-left: 1.75rem; }

    .tab-btn { padding: 0.75rem 1rem; font-size: 0.8rem; }
    .tab-content { padding: 1rem; }
}

/* Very small phones */
@media (max-width: 360px) {
    .stats-row { grid-template-columns: 1fr; }
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
    if (!el) return;
    el.classList.toggle('expanded');
    if (toggle) toggle.classList.toggle('expanded');
}
</script>
@endpush

@endsection