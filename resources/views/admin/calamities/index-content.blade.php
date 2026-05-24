<div class="dash-header">
    <h1>Calamity Meter</h1>
    <div style="display:flex;gap:10px;align-items:center;">
        <x-back-button href="{{ route('staff.dashboard') }}" label="Back" />
        @if(auth()->user()->role->name !== 'Staff')
        <a href="{{ route('admin.calamity.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Create
        </a>
        @endif
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
    @if(auth()->user()->role->name !== 'Staff')
    <a href="{{ route('admin.calamity.create') }}" class="btn-primary btn-large">
        <i class="fas fa-plus"></i> Create First
    </a>
    @endif
</div>
@else
<div class="portals-grid">
    @foreach($calamities as $calamity)
    <a href="{{ route(auth()->user()->role->name === 'Staff' ? 'staff.calamities.show' : 'admin.calamity.show', $calamity->id) }}" class="portal-card" style="text-decoration:none;">
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
                @if(auth()->user()->role->name !== 'Staff')
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
                @else
                <span class="staff-view-only">View Only</span>
                @endif
            </div>
        </div>
    </a>
    @endforeach
</div>
@endif
