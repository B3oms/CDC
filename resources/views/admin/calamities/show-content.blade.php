<div class="dash-header">
    <div>
        <h1>{{ $calamity->name }}</h1>
        <p class="sub">{{ $calamity->type }} · {{ $calamity->date_occurred }}</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <x-back-button href="{{ route(auth()->user()->role->name === 'Staff' ? 'staff.calamities.index' : 'admin.calamity.index') }}" label="Back" />
        @if($calamity->status === 'Open')
        <span class="status-open">● OPEN</span>
        @if(auth()->user()->role->name !== 'Staff')
        <form method="POST" action="{{ route('admin.calamity.close', $calamity->id) }}">
            @csrf
            <button type="submit" class="btn-danger" onclick="return confirm('Close this calamity and generate report?')">
                Close
            </button>
        </form>
        @endif
        @else
        <span class="status-closed">● CLOSED</span>
        @if(auth()->user()->role->name !== 'Staff')
        <x-pdf-export-dropdown export-onclick="exportPdf({{ $calamity->id }})" :landscape-default="true" />
        @endif
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="dash-grid">
    <div class="yearly-col">
        <div class="section-card">
            <h3>Partner Barangays</h3>
            <div class="barangay-list">
                @foreach($calamity->barangays as $barangay)
                @php
                    // Get the latest evacuation report for this barangay
                    $latestReport = $calamity->evacuationReports
                        ->where('barangay_id', $barangay->id)
                        ->first();
                @endphp
                <div class="barangay-card">
                    <div class="barangay-header">
                        <div class="barangay-name">
                            <span class="dot"></span>
                            <span>{{ $barangay->name }}</span>
                        </div>
                        @if($latestReport && $latestReport->evacuationCenter)
                        <div class="venue-badge">
                            ✓ Venue Reported
                        </div>
                        @else
                        <div class="venue-badge pending">
                            ⏳ Pending
                        </div>
                        @endif
                    </div>
                    @if($latestReport && $latestReport->evacuationCenter)
                    <div class="venue-info">
                        <div class="venue-name">
                            @if(!empty($latestReport->evacuationCenter->venue))
                                {{ $latestReport->evacuationCenter->venue }}
                            @else
                                {{ $latestReport->evacuationCenter->location }}
                            @endif
                        </div>
                        <div class="venue-location">{{ $latestReport->evacuationCenter->location }}</div>
                    </div>
                    @else
                    <div class="venue-info empty">
                        <div class="no-venue">No evacuation venue reported yet</div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="right-col">
        <div class="section-card">
            <h3>Live Rankings — Top 10 Barangays</h3>
            <table class="dist-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Barangay</th>
                        <th>Households</th>
                        <th>Evacuees</th>
                        <th>Severity</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rankings as $i => $r)
                    <tr class="{{ $i < 3 ? 'top-rank' : '' }}">
                        <td><strong>#{{ $i + 1 }}</strong></td>
                        <td>{{ $r->barangay->name }}</td>
                        <td>
                            {{ $r->total_households }}
                            <button onclick="toggleHouseholds({{ $r->barangay->id }})" class="btn-view-households" style="background: none; border: none; cursor: pointer; font-size: 14px; margin-left: 8px;">
                                <i class="fas fa-eye" id="icon-{{ $r->barangay->id }}"></i>
                            </button>
                        </td>
                        <td>{{ $r->total_evacuees }}</td>
                        <td>{{ $r->max_severity }}/5</td>
                        <td><strong>{{ number_format($r->score, 2) }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:#888;padding:16px;">
                            No reports submitted yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Household Details Section --}}
        <div id="household-details-section" style="display: none; margin-top: 1rem;">
            <div class="section-card">
                <h3 id="household-details-title">Household Details</h3>
                <div style="overflow-x: auto;">
                    <table class="dist-table" id="household-details-table">
                        <thead>
                            <tr>
                                <th>Household Head</th>
                                <th>Members</th>
                                <th>Household Code</th>
                                <th>Contact</th>
                            </tr>
                        </thead>
                        <tbody id="household-details-body">
                            <!-- Household data will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Partner Barangays Layout Styles */
.barangay-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.barangay-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 16px;
    transition: all 0.2s ease;
}

.barangay-card:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
}

.barangay-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.barangay-name {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 1rem;
    color: #333;
}

.venue-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    white-space: nowrap;
}

.venue-badge {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.venue-badge.pending {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.venue-info {
    padding-left: 24px;
}

.venue-info .venue-name {
    font-weight: 600;
    color: #495057;
    margin-bottom: 4px;
    font-size: 0.9rem;
}

.venue-info .venue-location {
    color: #6c757d;
    font-size: 0.85rem;
    line-height: 1.4;
}

.venue-info.empty {
    padding-left: 24px;
}

.no-venue {
    color: #6c757d;
    font-style: italic;
    font-size: 0.9rem;
}

.dot {
    width: 8px;
    height: 8px;
    background: #007bff;
    border-radius: 50%;
}

.status-open {
    background: #28a745;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-closed {
    background: #6c757d;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.btn-danger {
    background: #dc3545;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 0.8rem;
    cursor: pointer;
}

.btn-danger:hover {
    background: #c82333;
}

.btn-view-households {
    color: #007bff;
}

.btn-view-households:hover {
    color: #0056b3;
}

.top-rank {
    background: #fff3cd;
}

.dist-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

.dist-table th,
.dist-table td {
    padding: 8px 12px;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.dist-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.dist-table tr:hover {
    background: #f8f9fa;
}
</style>

<script>
function toggleHouseholds(barangayId) {
    const section = document.getElementById('household-details-section');
    const title = document.getElementById('household-details-title');
    const tbody = document.getElementById('household-details-body');
    const icon = document.getElementById('icon-' + barangayId);
    
    if (section.style.display === 'none') {
        // Show household details
        fetch(`/barangays/${barangayId}/households`)
            .then(response => response.json())
            .then(data => {
                title.textContent = `Household Details - ${data.barangay_name}`;
                tbody.innerHTML = data.households.map(household => `
                    <tr>
                        <td>${household.head_name}</td>
                        <td>${household.members_count}</td>
                        <td>${household.household_code}</td>
                        <td>${household.contact_number || 'N/A'}</td>
                    </tr>
                `).join('');
                section.style.display = 'block';
                icon.className = 'fas fa-eye-slash';
            })
            .catch(error => {
                console.error('Error fetching household details:', error);
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:red;">Error loading household details</td></tr>';
                section.style.display = 'block';
                icon.className = 'fas fa-eye-slash';
            });
    } else {
        // Hide household details
        section.style.display = 'none';
        icon.className = 'fas fa-eye';
    }
}

function exportPdf(calamityId) {
    const paperSize = document.getElementById('paperSize').value;
    const orientation = document.getElementById('orientation').value;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/calamity/${calamityId}/pdf`;
    form.style.display = 'none';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.getAttribute('content');
        form.appendChild(csrfInput);
    }
    
    const paperSizeInput = document.createElement('input');
    paperSizeInput.type = 'hidden';
    paperSizeInput.name = 'paper_size';
    paperSizeInput.value = paperSize;
    form.appendChild(paperSizeInput);
    
    const orientationInput = document.createElement('input');
    orientationInput.type = 'hidden';
    orientationInput.name = 'orientation';
    orientationInput.value = orientation;
    form.appendChild(orientationInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    closePdfDropdown('pdfOptions');
}
</script>
