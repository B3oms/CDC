@extends('admin.layouts.app')
@section('title', 'Calamity')

@section('content')
<div class="dash-header">
    <div>
        <h1>{{ $calamity->name }}</h1>
        <p class="sub">{{ $calamity->type }} · {{ $calamity->date_occurred }}</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <a href="{{ route('admin.calamity.index') }}" class="btn-back">← Back</a>
        @if($calamity->status === 'Open')
        <span class="status-open">● OPEN</span>
        <form method="POST" action="{{ route('admin.calamity.close', $calamity->id) }}">
            @csrf
            <button type="submit" class="btn-danger" onclick="return confirm('Close this calamity and generate report?')">
                Close
            </button>
        </form>
        @else
        <span class="status-closed">● CLOSED</span>
        <a href="{{ route('admin.calamity.report', $calamity->id) }}" class="btn-primary">View Report</a>
        <div class="pdf-export-dropdown" style="position:relative;display:inline-block;">
            <button onclick="togglePdfDropdown()" class="btn-export-pdf"
               style="display: inline-flex !important; align-items: center !important; gap: 6px !important; padding: 8px 16px !important; background: #10b981 !important; color: white !important; text-decoration: none !important; border-radius: 6px !important; font-size: 13px !important; font-weight: 500 !important; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3) !important; letter-spacing: 0.5px !important; border:none !important; cursor:pointer !important;"
               onmouseover="this.style.background='#059669'"
               onmouseout="this.style.background='#10b981'">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
            <div id="pdfOptions" class="pdf-options" style="display:none;position:absolute;top:100%;right:0;background:white;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);padding:12px;min-width:200px;z-index:1001;">
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Paper Size</label>
                    <select id="paperSize" style="width:100%;padding:6px 8px;border:1px solid #d1d5db;border-radius:4px;font-size:13px;color:#374151;">
                        <option value="A4">A4</option>
                        <option value="Letter">Letter</option>
                        <option value="Legal">Legal</option>
                    </select>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Orientation</label>
                    <select id="orientation" style="width:100%;padding:6px 8px;border:1px solid #d1d5db;border-radius:4px;font-size:13px;color:#374151;">
                        <option value="portrait">Portrait</option>
                        <option value="landscape" selected>Landscape</option>
                    </select>
                </div>
                <button onclick="exportPdf({{ $calamity->id }})" style="width:100%;padding:8px;background:#10b981;color:white;border:none;border-radius:4px;font-size:13px;font-weight:500;cursor:pointer;transition:background 0.2s;"
                   onmouseover="this.style.background='#059669'"
                   onmouseout="this.style.background='#10b981'">
                    Export PDF
                </button>
            </div>
        </div>
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
@endsection

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

.venue-info.empty .no-venue {
    color: #adb5bd;
    font-style: italic;
    font-size: 0.85rem;
}

/* Ensure dot styling works with new layout */
.barangay-name .dot {
    background: #007bff;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

.btn-export-pdf {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 8px 16px !important;
    background: #10b981 !important;
    color: white !important;
    text-decoration: none !important;
    border-radius: 6px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3) !important;
    letter-spacing: 0.5px !important;
}

.btn-export-pdf:hover {
    background: #059669 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 8px 12px -2px rgba(16, 185, 129, 0.4) !important;
    text-decoration: none !important;
    color: white !important;
}
</style>

@push('scripts')
<script>
let currentBarangayId = null;

function toggleHouseholds(barangayId) {
    const detailsSection = document.getElementById('household-details-section');
    const icon = document.getElementById('icon-' + barangayId);
    const title = document.getElementById('household-details-title');
    const tbody = document.getElementById('household-details-body');
    
    // Reset all icons
    document.querySelectorAll('[id^="icon-"]').forEach(i => {
        i.className = 'fas fa-eye';
    });
    
    // If clicking the same barangay, toggle visibility
    if (currentBarangayId === barangayId && detailsSection.style.display === 'block') {
        detailsSection.style.display = 'none';
        icon.className = 'fas fa-eye';
        currentBarangayId = null;
        return;
    }
    
    // Show loading state
    title.textContent = 'Loading household details...';
    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:20px;">Loading...</td></tr>';
    detailsSection.style.display = 'block';
    icon.className = 'fas fa-eye-slash';
    currentBarangayId = barangayId;
    
    // Fetch household data via AJAX
    const calamityId = {{ $calamity->id }};
    fetch(`/admin/calamity/households/${calamityId}/${barangayId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.error || 'Unknown error occurred');
            }
            
            // Update title with barangay name and total count
            title.textContent = `${data.barangay_name} - Household Details (${data.total_households} households)`;
            
            // Clear tbody
            tbody.innerHTML = '';
            
            if (data.households.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:20px;color:#888;">No households registered</td></tr>';
                return;
            }
            
            // Add household rows
            data.households.forEach(household => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><strong>${household.head_name || 'N/A'}</strong></td>
                    <td>${household.member_count || 0}</td>
                    <td>${household.household_code || 'N/A'}</td>
                    <td>${household.contact_number || 'N/A'}</td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error fetching households:', error);
            title.textContent = 'Error Loading Household Details';
            tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;padding:20px;color:#dc3545;">Failed to load household data: ${error.message}</td></tr>`;
        });
}

// PDF Export Functions
function togglePdfDropdown() {
    const dropdown = document.getElementById('pdfOptions');
    if (dropdown.style.display === 'none') {
        dropdown.style.display = 'block';
    } else {
        dropdown.style.display = 'none';
    }
}

function exportPdf(calamityId) {
    const paperSize = document.getElementById('paperSize').value;
    const orientation = document.getElementById('orientation').value;
    const url = `{{ route('admin.calamity.pdf', ':id') }}`.replace(':id', calamityId);
    const fullUrl = `${url}?paper_size=${paperSize}&orientation=${orientation}`;
    window.open(fullUrl, '_blank');
    document.getElementById('pdfOptions').style.display = 'none';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('pdfOptions');
    const button = event.target.closest('.pdf-export-dropdown');
    if (!button && dropdown && dropdown.style.display === 'block') {
        dropdown.style.display = 'none';
    }
});
</script>
@endpush