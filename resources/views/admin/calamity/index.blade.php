@section('content')
<div class="dash-header">
    <h1>Calamities</h1>
    <div style="display:flex;gap:10px;align-items:center;">
        <a href="{{ route('staff.dashboard') }}" class="btn-back">← Back</a>
        <a href="{{ route('admin.calamity.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Create
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert-error">{{ session('error') }}</div>
@endif

@if($calamities->isEmpty())
<div class="empty-state">
    <div class="empty-icon">
        <i class="fas fa-cloud-sun-rain"></i>
    </div>
    <h3>No Calamities Yet</h3>
    <p>Create your first calamity to start monitoring and managing disaster response operations.</p>
    <a href="{{ route('admin.calamity.create') }}" class="btn-primary btn-large">
        <i class="fas fa-plus"></i> Create First
    </a>
</div>
@else
<div class="portals-grid">
    @foreach($calamities as $calamity)
    <a href="{{ route('admin.calamity.show', $calamity->id) }}" class="portal-card" style="text-decoration:none;">
        <div class="portal-header">
            <div class="portal-info">
                <h3>{{ $calamity->name }}</h3>
                <div class="portal-meta">
                    <span class="portal-type">{{ $calamity->type }}</span>
                    <span class="portal-date">{{ \Carbon\Carbon::parse($calamity->date_occurred)->format('M d, Y') }}</span>
                </div>
            </div>
            <div class="portal-status">
                <span class="status-badge {{ strtolower($calamity->status) }}">
                    {{ $calamity->status }}
                </span>
                @if($calamity->status === 'Open')
                <span class="status-indicator active"></span>
                @endif
            </div>
        </div>
        
        <div class="portal-content">
            @if($calamity->description)
            <p class="portal-description">{{ Str::limit($calamity->description, 120) }}</p>
            @endif
            
            <div class="portal-stats">
                <div class="stat-item">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>{{ $calamity->barangays->count() }} Barangays</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-user"></i>
                    <span>{{ $calamity->creator ? $calamity->creator->first_name : 'Unknown' }}</span>
                </div>
            </div>
        </div>
        
        <div class="portal-footer">
            <span class="view-portal">Click to view →</span>
            <div class="portal-actions">
                @if($calamity->status === 'Open')
                <form action="{{ route('admin.calamity.close', $calamity->id) }}" method="POST" onclick="event.stopPropagation()">
                    @csrf
                    <button type="submit" class="btn-close" onclick="return confirm('Are you sure you want to close this calamity?')">
                        <i class="fas fa-times"></i> Close
                    </button>
                </form>
                @endif
                <form action="{{ route('admin.calamity.destroy', $calamity->id) }}" method="POST" onclick="event.stopPropagation()">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to delete this calamity and all its data? This action cannot be undone.')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </a>
    @endforeach
</div>
@endif

@endsection

@push('scripts')
<style>
/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.empty-icon {
    font-size: 4rem;
    color: #9ca3af;
    margin-bottom: 1rem;
}

.empty-state h3 {
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
}

.empty-state p {
    color: #6b7280;
    margin-bottom: 2rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

.btn-large {
    padding: 1rem 2rem;
    font-size: 1rem;
}

/* Portal Grid */
.portals-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1rem;
}

/* Portal Card */
.portal-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    height: 100%;
}

.portal-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.portal-header {
    padding: 1rem;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.portal-info h3 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    line-height: 1.2;
}

.portal-meta {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.portal-type {
    background: #f3f4f6;
    color: #6b7280;
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 500;
}

.portal-date {
    color: #9ca3af;
    font-size: 0.75rem;
}

.portal-status {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.status-badge {
    padding: 0.3rem 0.6rem;
    border-radius: 12px;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.open {
    background: #dcfce7;
    color: #166534;
}

.status-badge.closed {
    background: #f3f4f6;
    color: #6b7280;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #d1d5db;
}

.status-indicator.active {
    background: #10b981;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.portal-content {
    padding: 1rem;
    flex: 1;
}

.portal-description {
    color: #6b7280;
    margin-bottom: 0.75rem;
    line-height: 1.4;
    font-size: 0.8rem;
}

.portal-stats {
    display: flex;
    gap: 1rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    color: #6b7280;
    font-size: 0.75rem;
}

.stat-item i {
    color: #9ca3af;
    font-size: 0.7rem;
}

.portal-footer {
    padding: 0.75rem 1rem;
    background: #f9fafb;
    border-top: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.view-portal {
    color: #3b82f6;
    font-weight: 500;
    font-size: 0.75rem;
}

.portal-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.btn-close {
    padding: 0.3rem 0.6rem;
    border: none;
    border-radius: 4px;
    background: #ef4444;
    color: white;
    font-size: 0.65rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.2rem;
}

.btn-delete {
    padding: 0.3rem 0.6rem;
    border: none;
    border-radius: 4px;
    background: #dc2626;
    color: white;
    font-size: 0.65rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.2rem;
}

.btn-delete:hover {
    background: #b91c1c;
    transform: scale(1.05);
}

.btn-close:hover {
    background: #dc2626;
}

/* Alerts */
.alert-success {
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    color: #166534;
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1rem;
}

.alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1rem;
}

/* Back Button */
.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: 1px solid #d1d5db;
    background: white;
    color: #6b7280;
    border-radius: 6px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-back:hover {
    background: #f9fafb;
    border-color: #9ca3af;
    color: #374151;
}

/* Primary Button */
.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #1a3d1f;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    text-decoration: none;
    transition: background 0.2s ease;
}

.btn-primary:hover {
    background: #145222;
}
</style>
@endpush
