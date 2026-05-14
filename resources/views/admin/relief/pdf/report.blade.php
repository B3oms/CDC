<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relief Monitor Report</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #2c2c2a;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1a3d1f;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1a3d1f;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #6b7280;
            font-size: 14px;
        }
        .stats-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }
        .stat-card {
            flex: 1;
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .stat-num {
            font-size: 28px;
            font-weight: bold;
            color: #1a3d1f;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a3d1f;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        .event-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .event-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        .event-name {
            font-weight: bold;
            font-size: 16px;
            color: #2c2c2a;
            margin: 0;
        }
        .event-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
        }
        .status-ongoing {
            background: #10b981;
            color: white;
        }
        .status-upcoming {
            background: #f59e0b;
            color: white;
        }
        .status-done {
            background: #6b7280;
            color: white;
        }
        .event-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }
        .detail-item {
            font-size: 11px;
        }
        .detail-label {
            font-weight: 600;
            color: #6b7280;
            margin-right: 5px;
        }
        .barangays {
            margin-top: 10px;
            font-size: 11px;
            color: #6b7280;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relief Monitor Report</h1>
        <p>Generated on {{ now()->format('F d, Y H:i:s') }}</p>
    </div>

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-num">{{ $ongoingCount }}</div>
            <div class="stat-label">Ongoing Events</div>
        </div>
        <div class="stat-card">
            <div class="stat-num">{{ $upcomingCount }}</div>
            <div class="stat-label">Upcoming Events</div>
        </div>
        <div class="stat-card">
            <div class="stat-num">{{ $completedCount }}</div>
            <div class="stat-label">Completed Events</div>
        </div>
        <div class="stat-card">
            <div class="stat-num">{{ $totalBeneficiaries }}</div>
            <div class="stat-label">Total Beneficiaries</div>
        </div>
    </div>

    @if($events->where('status', 'Ongoing')->count())
    <div class="section">
        <h2 class="section-title">Ongoing Events</h2>
        @foreach($events->where('status', 'Ongoing') as $event)
        <div class="event-card">
            <div class="event-header">
                <h3 class="event-name">{{ $event->name }}</h3>
                <span class="event-status status-ongoing">{{ $event->status }}</span>
            </div>
            <div class="event-details">
                <div class="detail-item">
                    <span class="detail-label">Date:</span>
                    {{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}
                </div>
                <div class="detail-item">
                    <span class="detail-label">Venue:</span>
                    {{ $event->venue }}
                </div>
                <div class="detail-item">
                    <span class="detail-label">Calamity:</span>
                    {{ $event->calamity->name ?? 'N/A' }}
                </div>
                <div class="detail-item">
                    <span class="detail-label">Beneficiaries:</span>
                    {{ $event->eventBarangays->sum(function($eb) { return $eb->beneficiary->count(); }) }}
                </div>
            </div>
            <div class="barangays">
                <strong>Coverage Areas:</strong>
                @foreach($event->eventBarangays as $eb)
                    {{ $eb->barangay->name }}, {{ $eb->municipality->name }}@if(!$loop->last); @endif
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @if($events->where('status', 'Upcoming')->count())
    <div class="section">
        <h2 class="section-title">Upcoming Events</h2>
        @foreach($events->where('status', 'Upcoming') as $event)
        <div class="event-card">
            <div class="event-header">
                <h3 class="event-name">{{ $event->name }}</h3>
                <span class="event-status status-upcoming">{{ $event->status }}</span>
            </div>
            <div class="event-details">
                <div class="detail-item">
                    <span class="detail-label">Date:</span>
                    {{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}
                </div>
                <div class="detail-item">
                    <span class="detail-label">Venue:</span>
                    {{ $event->venue }}
                </div>
                <div class="detail-item">
                    <span class="detail-label">Calamity:</span>
                    {{ $event->calamity->name ?? 'N/A' }}
                </div>
                <div class="detail-item">
                    <span class="detail-label">Beneficiaries:</span>
                    {{ $event->eventBarangays->sum(function($eb) { return $eb->beneficiary->count(); }) }}
                </div>
            </div>
            <div class="barangays">
                <strong>Coverage Areas:</strong>
                @foreach($event->eventBarangays as $eb)
                    {{ $eb->barangay->name }}, {{ $eb->municipality->name }}@if(!$loop->last); @endif
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @if($events->where('status', 'Done')->count())
    <div class="section">
        <h2 class="section-title">Completed Events</h2>
        @foreach($events->where('status', 'Done') as $event)
        <div class="event-card">
            <div class="event-header">
                <h3 class="event-name">{{ $event->name }}</h3>
                <span class="event-status status-done">{{ $event->status }}</span>
            </div>
            <div class="event-details">
                <div class="detail-item">
                    <span class="detail-label">Date:</span>
                    {{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}
                </div>
                <div class="detail-item">
                    <span class="detail-label">Venue:</span>
                    {{ $event->venue }}
                </div>
                <div class="detail-item">
                    <span class="detail-label">Calamity:</span>
                    {{ $event->calamity->name ?? 'N/A' }}
                </div>
                <div class="detail-item">
                    <span class="detail-label">Beneficiaries:</span>
                    {{ $event->eventBarangays->sum(function($eb) { return $eb->beneficiary->count(); }) }}
                </div>
            </div>
            <div class="barangays">
                <strong>Coverage Areas:</strong>
                @foreach($event->eventBarangays as $eb)
                    {{ $eb->barangay->name }}, {{ $eb->municipality->name }}@if(!$loop->last); @endif
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        <p>Relief Monitoring and Distribution System - Generated Report</p>
        <p>This report was automatically generated on {{ now()->format('F d, Y H:i:s') }}</p>
    </div>
</body>
</html>
