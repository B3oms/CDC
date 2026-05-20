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
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1a3d1f;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            margin: 0;
            color: #1a3d1f;
            font-weight: 700;
        }
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 11px;
        }
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
        .event-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 15px;
        }
        .event-detail {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
        }
        .event-detail strong {
            color: #1a3d1f;
            font-weight: 600;
        }
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
    <div class="header">
        <h1>RELIEF EVENT REPORT</h1>
        <p>SPUP-CDC Disaster Response System</p>
        <p>Generated: {{ $generated_date }}</p>
    </div>

    <div class="event-info">
        <h3>{{ $event->name }}</h3>
        <div class="event-details">
            <div class="event-detail">
                <strong>Date:</strong>
                <span>{{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}</span>
            </div>
            <div class="event-detail">
                <strong>Status:</strong>
                <span>{{ $event->status }}</span>
            </div>
            <div class="event-detail">
                <strong>Venue:</strong>
                <span>{{ $event->venue }}</span>
            </div>
            <div class="event-detail">
                <strong>Calamity:</strong>
                <span>{{ $event->calamity ? $event->calamity->name : 'N/A' }}</span>
            </div>
            <div class="event-detail">
                <strong>Created By:</strong>
                <span>{{ $event->creator ? $event->creator->first_name . ' ' . $event->creator->last_name : 'N/A' }}</span>
            </div>
            <div class="event-detail">
                <strong>Created Date:</strong>
                <span>{{ \Carbon\Carbon::parse($event->created_at)->format('F d, Y') }}</span>
            </div>
        </div>
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
        <div class="event-details">
            <div class="event-detail">
                <strong>Total Beneficiaries:</strong>
                <span>{{ $event->eventBarangays->sum('beneficiary_count') }}</span>
            </div>
            <div class="event-detail">
                <strong>Total Barangays:</strong>
                <span>{{ $event->eventBarangays->count() }}</span>
            </div>
            <div class="event-detail">
                <strong>Total Facilitators:</strong>
                <span>{{ $event->eventBarangays->sum(function($eb) { return $eb->facilitators->count(); }) }}</span>
            </div>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>This report was generated automatically by the SPUP-CDC Disaster Response System</p>
        <p>For questions or concerns, please contact the CDC office</p>
    </div>
</body>
</html>
