<!DOCTYPE html>
<html>
<head>
    <title>Relief Event Report</title>
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
        .event-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #1a3d1f;
        }
        .event-info h3 {
            margin: 0 0 15px 0;
            color: #1a3d1f;
            font-size: 16px;
            font-weight: 600;
        }
        .event-detail-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .event-detail-table td { padding: 4px 6px; vertical-align: top; width: 50%; font-size: 11px; }
        .event-detail-table strong { color: #1a3d1f; font-weight: 600; margin-right: 4px; }
        .barangay-section {
            margin-bottom: 25px;
        }
        .barangay-section h3 {
            color: #1a3d1f;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: 600;
        }
        .barangay-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
        }
        .barangay-table th,
        .barangay-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        .barangay-table th {
            background-color: #1a3d1f;
            color: white;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        .barangay-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .summary-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #1a3d1f;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        .summary-item {
            text-align: center;
        }
        .summary-number {
            font-size: 18px;
            font-weight: bold;
            color: #1a3d1f;
        }
        .summary-label {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
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
                    <div style="font-size: 20px; font-weight: bold; color: #1a3d1f;">RELIEF EVENT REPORT</div>
                    <div style="font-size: 10px; color: #9ca3af; margin-top: 2px;">Generated: {{ $generated_date }}</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <span class="rpt-badge">Official Report</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="event-info">
        <h3>{{ $event->name }}</h3>
        <table class="event-detail-table">
            <tr>
                <td><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}</td>
                <td><strong>Status:</strong> {{ $event->status }}</td>
            </tr>
            <tr>
                <td><strong>Venue:</strong> {{ $event->venue }}</td>
                <td><strong>Calamity:</strong> {{ $event->calamity ? $event->calamity->name : 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Created By:</strong> {{ $event->creator ? $event->creator->first_name . ' ' . $event->creator->last_name : 'N/A' }}</td>
                <td><strong>Created Date:</strong> {{ \Carbon\Carbon::parse($event->created_at)->format('F d, Y') }}</td>
            </tr>
        </table>
        @if($event->description)
        <div style="margin-top: 15px;">
            <strong>Description:</strong>
            <p>{{ $event->description }}</p>
        </div>
        @endif
    </div>

    <div class="barangay-section">
        <h3>Participating Barangays</h3>
        <table class="barangay-table">
            <thead>
                <tr>
                    <th>Barangay</th>
                    <th>Municipality</th>
                    <th>Beneficiaries</th>
                    <th>Facilitators</th>
                </tr>
            </thead>
            <tbody>
                @foreach($event->eventBarangays as $eventBarangay)
                <tr>
                    <td>{{ $eventBarangay->barangay->name }}</td>
                    <td>{{ $eventBarangay->municipality->name ?? 'N/A' }}</td>
                    <td>{{ $eventBarangay->beneficiary_count ?? 0 }}</td>
                    <td>{{ $eventBarangay->facilitators->count() ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($event->eventBarangays->isNotEmpty())
    <div class="barangay-section">
        <h3>Beneficiary Summary</h3>
        <table class="event-detail-table">
            <tr>
                <td><strong>Total Beneficiaries:</strong> {{ $event->eventBarangays->sum('beneficiary_count') }}</td>
                <td><strong>Total Barangays:</strong> {{ $event->eventBarangays->count() }}</td>
            </tr>
            <tr>
                <td><strong>Total Facilitators:</strong> {{ $event->eventBarangays->sum(function($eb) { return $eb->facilitators->count(); }) }}</td>
                <td></td>
            </tr>
        </table>
    </div>
    @endif

    <div class="footer">
        <strong style="color: #6b7280; font-size: 10px;">SPUP-CDC Disaster Response System</strong><br>
        This is a system-generated document &bull; {{ $generated_date }}
    </div>
</body>
</html>
