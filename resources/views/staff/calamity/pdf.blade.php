<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $calamity->name }} - Calamity Portal Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
            line-height: 1.4;
        }
        .top-bar { background-color: #1a3d1f; height: 5px; margin-bottom: 0; }
        .page-header { padding: 15px 0 14px; margin-bottom: 20px; border-bottom: 1px solid #dee2e6; }
        .header-tbl { width: 100%; border-collapse: collapse; }
        .org-lbl { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
        .rpt-badge { background-color: #1a3d1f; color: white; padding: 5px 12px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .info-section {
            margin-bottom: 30px;
        }
        .info-grid-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-grid-table td { padding: 4px; vertical-align: top; width: 50%; }
        .info-item {
            padding: 12px;
            background: #f8f9fa;
            border-left: 4px solid #1a3d1f;
            border-radius: 4px;
        }
        .info-item strong {
            color: #1a3d1f;
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1a3d1f;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .barangay-list {
            margin-bottom: 30px;
        }
        .barangay-item {
            padding: 10px;
            margin-bottom: 8px;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #1a3d1f;
        }
        .barangay-name {
            font-weight: bold;
            color: #1a3d1f;
            font-size: 13px;
        }
        .venue-info {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #1a3d1f;
            color: white;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .top-rank {
            background-color: #fff3cd;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-open {
            background-color: #d1fae5;
            color: #059669;
        }
        .status-closed {
            background-color: #fee2e2;
            color: #dc2626;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .no-data {
            text-align: center;
            color: #888;
            font-style: italic;
            padding: 20px;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="top-bar"></div>
    <div class="page-header">
        <table class="header-tbl">
            <tr>
                <td style="vertical-align: middle;">
                    <div class="org-lbl">SPUP-CDC Disaster Response System</div>
                    <div style="font-size: 19px; font-weight: bold; color: #1a3d1f;">{{ $calamity->name }}</div>
                    <div style="font-size: 10px; color: #9ca3af; margin-top: 2px;">Calamity Report &bull; Generated: {{ $generated_date }}</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <span class="rpt-badge">Official Report</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="info-section">
        <table class="info-grid-table">
            <tr>
                <td><div class="info-item"><strong>Calamity Type</strong>{{ $calamity->type }}</div></td>
                <td><div class="info-item"><strong>Date Occurred</strong>{{ \Carbon\Carbon::parse($calamity->date_occurred)->format('F d, Y') }}</div></td>
            </tr>
            <tr>
                <td><div class="info-item"><strong>Status</strong><span class="status-badge {{ $calamity->status === 'Open' ? 'status-open' : 'status-closed' }}">{{ $calamity->status }}</span></div></td>
                <td><div class="info-item"><strong>Total Partner Barangays</strong>{{ $calamity->barangays->count() }}</div></td>
            </tr>
        </table>
        @if($calamity->description)
        <div class="info-item" style="margin-bottom: 15px;">
            <strong>Description</strong>
            {{ $calamity->description }}
        </div>
        @endif
    </div>

    <div class="section-title">Partner Barangays</div>
    @if($calamity->barangays && $calamity->barangays->count() > 0)
        @foreach($calamity->barangays as $barangay)
            @php
                $latestReport = $calamity->evacuationReports
                    ->where('barangay_id', $barangay->id)
                    ->first();
            @endphp
            <div style="margin-bottom: 25px; page-break-inside: avoid;">
                <div class="barangay-item" style="margin-bottom: 10px;">
                    <div class="barangay-name">{{ $barangay->name }}</div>
                                        @if($latestReport && $latestReport->evacuationCenter)
                        <div class="venue-info">
                            Venue: @if(!empty($latestReport->evacuationCenter->venue))
                                {{ $latestReport->evacuationCenter->venue }}
                            @else
                                {{ $latestReport->evacuationCenter->location }}
                            @endif
                        </div>
                        <div class="venue-info">
                            Location: {{ $latestReport->evacuationCenter->location }}
                        </div>
                    @else
                        <div class="venue-info">No evacuation venue reported yet</div>
                    @endif
                </div>
                
                            </div>
        @endforeach
    @else
        <div class="no-data">No partner barangays assigned</div>
    @endif

    <div class="section-title">Live Rankings — Top 10 Barangays</div>
    @if($rankings && $rankings->count() > 0)
        <table>
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
                @foreach($rankings as $i => $ranking)
                    <tr class="{{ $i < 3 ? 'top-rank' : '' }}">
                        <td><strong>#{{ $i + 1 }}</strong></td>
                        <td>{{ $ranking->barangay->name }}</td>
                        <td>{{ $ranking->total_households }}</td>
                        <td>{{ $ranking->total_evacuees }}</td>
                        <td>{{ $ranking->max_severity }}/5</td>
                        <td><strong>{{ number_format($ranking->score, 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="font-size: 11px; color: #666; margin-top: 10px;">
            <strong>Score Calculation:</strong> (Evacuees × 0.6) + (Households × 0.2) + (Severity × 0.2)
        </div>
    @else
        <div class="no-data">No reports submitted yet</div>
    @endif

    <div class="footer">
        <strong style="color: #6b7280; font-size: 10px;">SPUP-CDC Disaster Response System</strong><br>
        Calamity Monitoring Report &bull; This is a system-generated document
    </div>
</body>
</html>

</file>
