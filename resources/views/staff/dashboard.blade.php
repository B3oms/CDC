@extends('staff.layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="dash-header">
    <h1>Hello, {{ auth()->user()->first_name }}!</h1>

    <a href="{{ route('admin.calamity.index') }}"
    class="calamity-meter {{ $activeCalamity ? 'active' : 'none' }}"
    style="text-decoration:none;">
    <div class="cal-label">Calamity Meter ↗</div>
    @if($activeCalamity)
    <div class="cal-name">{{ $activeCalamity->name }}</div>
    <span class="cal-badge">Active</span>
    @else
    <div class="cal-name">View All Portals</div>
    @endif
</a>
</div>

{{-- Stats Row --}}
<div class="stats-row" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-num">{{ $barangayCount }}</div>
        <div class="stat-label">Barangays</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $municipalityCount }}</div>
        <div class="stat-label">Municipalities</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#185fa5;">
            {{ $upcomingEvents->count() }}
        </div>
        <div class="stat-label">Upcoming Events</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#3b6d11;">
            {{ $completedEvents->count() }}
        </div>
        <div class="stat-label">Completed Events</div>
    </div>
</div>

<div class="dash-grid">
    {{-- LEFT: Charts --}}
    <div class="yearly-col">
        <div class="chart-card">
            <div class="chart-title">MONTHLY TREND</div>
            <canvas id="chart-monthly" height="100" style="height: 100px; width: 100%;"></canvas>
            <button onclick="exportChartToPDF('chart-monthly', 'monthly-trend')" class="pdf-export-btn">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
        </div>

        @forelse($yearlyData as $year => $months)
{{-- Yearly Trend Chart --}}
        <div class="chart-card">
            <div class="chart-title">YEARLY TREND</div>
            <canvas id="chart-yearly-trend"
                data-labels="{{ json_encode($yearlyTrendLabels) }}"
                data-values="{{ json_encode($yearlyTrendValues) }}"
                height="80" style="height: 80px; width: 100%;">
            </canvas>
            <div class="chart-actions">
                <button onclick="exportChartToPDF('chart-yearly-trend', 'yearly-trend')" class="pdf-export-btn">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
                <a href="{{ route('admin.relief.index') }}" class="view-link">View →</a>
            </div>
        </div>
        @empty
        <div class="chart-card">
            <div class="chart-title">No relief data yet</div>
            <p style="font-size:12px;color:#888;margin-top:8px;">
                Create relief events to see charts.
            </p>
        </div>
        @endforelse
    </div>

    {{-- RIGHT --}}
    <div class="right-col">

        {{-- Staff --}}
        <div class="section-card">
            <h3>Staff</h3>
            <div class="staff-row">
                @forelse($staff as $s)
                <div class="staff-item">
                    <div class="avatar">
                        {{ strtoupper(substr($s->first_name,0,1).substr($s->last_name,0,1)) }}
                    </div>
                    <div>
                        <div class="staff-name">{{ $s->first_name }} {{ $s->last_name }}</div>
                        <div class="staff-role">{{ $s->role->name }}</div>
                    </div>
                </div>
                @empty
                <p style="font-size:12px;color:#888;">No staff found.</p>
                @endforelse
            </div>
        </div>

        {{-- Upcoming & Ongoing --}}
        @if($upcomingEvents->count())
        <div class="section-card">
            <h3>Upcoming & Ongoing Relief Events</h3>
            <table class="dist-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Barangay</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcomingEvents as $event)
                    <tr>
                        <td>
                            <a href="{{ route('admin.relief.show', $event->id) }}"
                                style="color:#185fa5;text-decoration:none;">
                                {{ $event->name }}
                            </a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</td>
                        <td>
                            @foreach($event->eventBarangays as $eb)
                                {{ $eb->barangay->name }}@if(!$loop->last), @endif
                            @endforeach
                        </td>
                        <td>
                            <span class="relief-status-badge {{ strtolower($event->status) }}">
                                {{ $event->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <a href="{{ route('admin.relief.index') }}" class="see-all">See all →</a>
        </div>
        @endif

        {{-- Completed --}}
        @if($completedEvents->count())
        <div class="section-card">
            <h3>Completed Relief Events</h3>
            <table class="dist-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Barangay</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($completedEvents->take(5) as $event)
                    <tr>
                        <td>
                            <a href="{{ route('admin.relief.show', $event->id) }}"
                                style="color:#185fa5;text-decoration:none;">
                                {{ $event->name }}
                            </a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</td>
                        <td>
                            @foreach($event->eventBarangays as $eb)
                                {{ $eb->barangay->name }}@if(!$loop->last), @endif
                            @endforeach
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <a href="{{ route('admin.relief.index') }}" class="see-all">See all →</a>
        </div>
        @endif

        {{-- Partners --}}
        <div class="section-card">
            <h3>Partners</h3>
            <div class="partner-row">
                <div class="p-stat">
                    <div class="p-num">{{ $barangayCount }}</div>
                    <div class="p-label">Barangays</div>
                </div>
                <div class="p-stat">
                    <div class="p-num">{{ $municipalityCount }}</div>
                    <div class="p-label">Municipalities</div>
                </div>
                <div class="p-stat">
                    <div class="p-num">{{ $regionCount }}</div>
                    <div class="p-label">Regions</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Chart
    const monthlyCtx = document.getElementById('chart-monthly');
    if (monthlyCtx) {
        // Process actual database data
        const monthlyRaw = @json($monthlyData);
        const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        const monthlyValues = monthNames.map((_, i) => {
            const found = monthlyRaw.find(m => m.month === i + 1);
            return found ? found.total : 0;
        });

        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthNames,
                datasets: [{
                    label: 'Relief Events',
                    data: monthlyValues,
                    backgroundColor: 'rgba(26, 61, 31, 0.7)',
                    borderColor: 'rgba(26, 61, 31, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Yearly Trend Chart
    const yearlyTrendCanvas = document.getElementById('chart-yearly-trend');
    if (yearlyTrendCanvas && yearlyTrendCanvas.dataset.labels && yearlyTrendCanvas.dataset.values) {
        const yearLabels = JSON.parse(yearlyTrendCanvas.dataset.labels);
        const yearValues = JSON.parse(yearlyTrendCanvas.dataset.values);
        
        new Chart(yearlyTrendCanvas, {
            type: 'line',
            data: {
                labels: yearLabels,
                datasets: [{
                    label: 'Relief Events',
                    data: yearValues,
                    borderColor: '#1a3d1f',
                    backgroundColor: 'rgba(26, 61, 31, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});

// PDF Export Function
function exportChartToPDF(chartId, filename) {
    const { jsPDF } = window.jspdf;
    const chartElement = document.getElementById(chartId);
    
    // Get chart title for PDF header
    const chartTitle = chartElement.closest('.chart-card').querySelector('.chart-title').textContent;
    
    html2canvas(chartElement, {
        backgroundColor: '#ffffff',
        scale: 3,
        useCORS: true,
        allowTaint: true
    }).then(canvas => {
        const imgData = canvas.toDataURL('image/png', 1.0);
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });
        
        // PDF dimensions
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();
        const margin = 20;
        
        // Calculate optimal image dimensions
        const maxWidth = pageWidth - (margin * 2);
        const maxHeight = pageHeight - (margin * 3); // Leave space for title
        
        // Calculate image dimensions maintaining aspect ratio
        const imgWidth = maxWidth;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        
        // Adjust if image is too tall
        let finalHeight = imgHeight;
        if (imgHeight > maxHeight) {
            finalHeight = maxHeight;
            const finalWidth = (canvas.width * finalHeight) / canvas.height;
            // Recenter if width changed
            const xPosition = (pageWidth - finalWidth) / 2;
            
            // Add title
            pdf.setFontSize(16);
            pdf.setFont('helvetica', 'bold');
            pdf.text(chartTitle, pageWidth / 2, margin, { align: 'center' });
            
            // Add chart image
            pdf.addImage(imgData, 'PNG', xPosition, margin + 15, finalWidth, finalHeight);
        } else {
            // Add title
            pdf.setFontSize(16);
            pdf.setFont('helvetica', 'bold');
            pdf.text(chartTitle, pageWidth / 2, margin, { align: 'center' });
            
            // Add chart image centered
            const xPosition = (pageWidth - imgWidth) / 2;
            pdf.addImage(imgData, 'PNG', xPosition, margin + 15, imgWidth, finalHeight);
        }
        
        // Add footer with timestamp
        pdf.setFontSize(10);
        pdf.setFont('helvetica', 'normal');
        const timestamp = new Date().toLocaleString();
        pdf.text(`Generated: ${timestamp}`, pageWidth / 2, pageHeight - 10, { align: 'center' });
        
        // Save PDF with professional filename
        const cleanFilename = filename.replace(/[^a-z0-9]/gi, '_').toLowerCase();
        pdf.save(`${cleanFilename}_report_${new Date().toISOString().split('T')[0]}.pdf`);
    });
}
</script>
@endpush

@push('styles')
<style>
/* PDF Export Buttons */
.pdf-export-btn {
    background: #dc3545;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    margin-top: 8px;
    transition: background-color 0.3s;
}

.pdf-export-btn:hover {
    background: #c82333;
}

.pdf-export-btn i {
    margin-right: 4px;
}

.chart-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
}

.view-link {
    color: #1a3d1f;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
}

.view-link:hover {
    text-decoration: underline;
}

/* Smaller calamity meter */
.calamity-meter {
    background: #faeeda;
    border: 1px solid #ef9f27;
    border-radius: 6px;
    padding: 8px 12px;
    text-align: right;
    min-width: 140px;
    font-size: 0.85rem;
}

.calamity-meter .cal-label {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #b8860b;
    margin-bottom: 2px;
    font-weight: 500;
}

.calamity-meter .cal-name {
    font-size: 11px;
    font-weight: 600;
    color: #633806;
    line-height: 1.2;
}

.calamity-meter .cal-badge {
    display: inline-block;
    background: #e24b4a;
    color: #fff;
    font-size: 8px;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 500;
    margin-top: 2px;
}

.calamity-meter.none {
    background: #eaf3de;
    border-color: #639922;
}

.calamity-meter.none .cal-label { color: #3b6d11; }
.calamity-meter.none .cal-name  { color: #27500a; }
</style>
@endpush