@extends('staff.layouts.app')
@section('title', 'Calamity')

@section('content')
<div class="dash-header">
    <div>
        <h1>{{ $calamity->name }}</h1>
        <p class="sub">{{ $calamity->type }} · {{ $calamity->date_occurred }}</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <x-back-button href="{{ route('admin.calamity.index') }}" label="Back" />
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
        <x-pdf-export-dropdown export-onclick="exportPdf({{ $calamity->id }})" :landscape-default="true" />
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

/* Additional styles for dash-grid layout */
.dash-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
}

.yearly-col {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.right-col {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.section-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
}

.section-card h3 {
    margin: 0 0 1rem 0;
    font-size: 1.25rem;
    color: #1f2937;
}

.dist-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    overflow: hidden;
}

.dist-table th,
.dist-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.dist-table th {
    background: #f9fafb;
    font-weight: 600;
    color: #1f2937;
}

.dist-table tbody tr:hover {
    background: #f9fafb;
}

.top-rank {
    background: #fef3cd;
}

.sub {
    color: #6b7280;
    margin: 0.25rem 0 0 0;
    font-size: 0.9rem;
}

.btn-danger {
    background: #dc2626;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s ease;
}

.btn-danger:hover {
    background: #b91c1c;
}

.alert-success {
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    color: #166534;
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1rem;
}

.status-open {
    background: #dcfce7;
    color: #166534;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-closed {
    background: #f3f4f6;
    color: #6b7280;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
</style>

@push('scripts')
<script>
let currentBarangayId = null;

function toggleHouseholds(barangayId) {
    const detailsSection = document.getElementById('household-details-section');
    const title = document.getElementById('household-details-title');
    const tbody = document.getElementById('household-details-body');
    const icon = document.getElementById('icon-' + barangayId);
    
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
    const isStaff = '{{ auth()->user()->role->name }}' === 'Staff';
    const route = isStaff 
        ? `/staff/calamities/households/${calamityId}/${barangayId}`
        : `/admin/calamity/households/${calamityId}/${barangayId}`;
    fetch(route)
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
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:20px;color:#888;">No households found in this evacuation report</td></tr>';
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

function exportPdf(calamityId) {
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = `{{ route('admin.calamity.pdf', ':id') }}`.replace(':id', calamityId);
    form.style.display = 'none';

    [['paper_size', document.getElementById('paperSize').value], ['orientation', document.getElementById('orientation').value]].forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    closePdfDropdown('pdfOptions');
}
</script>
@endpush
