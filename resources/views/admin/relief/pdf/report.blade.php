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
        .top-bar { background-color: #1a3d1f; height: 5px; margin-bottom: 16px; }
        .header-tbl { width: 100%; border-collapse: collapse; margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px solid #dee2e6; }
        .org-name { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
        .rpt-badge { background-color: #1a3d1f; color: white; padding: 5px 12px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .stats-tbl { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
        .stats-tbl td { padding: 5px; vertical-align: top; width: 25%; }
        .stat-card { background-color: #f0f7f0; border: 1px solid #c9d7c9; border-top: 3px solid #1a3d1f; padding: 12px; text-align: center; }
        .stat-card.ongoing { border-top-color: #059669; }
        .stat-card.upcoming { border-top-color: #d97706; }
        .stat-card.done { border-top-color: #6b7280; }
        .stat-num { font-size: 24px; font-weight: bold; color: #1a3d1f; margin-bottom: 4px; }
        .stat-card.ongoing .stat-num { color: #059669; }
        .stat-card.upcoming .stat-num { color: #d97706; }
        .stat-card.done .stat-num { color: #6b7280; }
        .stat-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        .section {
            margin-bottom: 30px;
        }
        .section-title { font-size: 11px; font-weight: bold; color: #1a3d1f; background-color: #f0f7f0; border-left: 4px solid #1a3d1f; padding: 6px 10px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.3px; }
        .section-title.ongoing { border-left-color: #059669; color: #065f46; }
        .section-title.upcoming { border-left-color: #d97706; color: #92400e; }
        .section-title.done { border-left-color: #6b7280; color: #374151; }
        .event-card { background-color: #ffffff; border: 1px solid #e5e7eb; border-left: 3px solid #1a3d1f; padding: 12px; margin-bottom: 10px; }
        .event-card.ongoing { border-left-color: #059669; }
        .event-card.upcoming { border-left-color: #d97706; }
        .event-card.done { border-left-color: #6b7280; }
        .event-name-tbl { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
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
        .status-ongoing { background-color: #d1fae5; color: #065f46; }
        .status-upcoming { background-color: #fef3c7; color: #92400e; }
        .status-done { background-color: #f3f4f6; color: #374151; }
        .event-details-tbl { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .event-details-tbl td { padding: 2px 4px; font-size: 10px; vertical-align: top; width: 50%; }
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
        .footer { margin-top: 30px; padding-top: 10px; border-top: 2px solid #dee2e6; text-align: center; color: #9ca3af; font-size: 9px; }
    </style>
</head>
<body>
    <div class="top-bar"></div>
    <table class="header-tbl">
        <tr>
            <td style="vertical-align: middle;">
                <div class="org-name">SPUP-CDC Disaster Response System</div>
                <div style="font-size: 20px; font-weight: bold; color: #1a3d1f;">Relief Monitor Report</div>
                <div style="font-size: 10px; color: #9ca3af; margin-top: 3px;">Generated: {{ now()->format('F d, Y H:i:s') }}</div>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <span class="rpt-badge">Official Report</span>
            </td>
        </tr>
    </table>

    <table class="stats-tbl">
        <tr>
            <td><div class="stat-card ongoing"><div class="stat-num">{{ $ongoingCount }}</div><div class="stat-label">Ongoing Events</div></div></td>
            <td><div class="stat-card upcoming"><div class="stat-num">{{ $upcomingCount }}</div><div class="stat-label">Upcoming Events</div></div></td>
            <td><div class="stat-card done"><div class="stat-num">{{ $completedCount }}</div><div class="stat-label">Completed Events</div></div></td>
            <td><div class="stat-card"><div class="stat-num">{{ $totalBeneficiaries }}</div><div class="stat-label">Total Beneficiaries</div></div></td>
        </tr>
    </table>

    @if($events->where('status', 'Ongoing')->count())
    <div class="section">
        <div class="section-title ongoing">Ongoing Events</div>
        @foreach($events->where('status', 'Ongoing') as $event)
        <div class="event-card ongoing">
            <table class="event-name-tbl"><tr>
                <td style="vertical-align: middle;"><strong style="font-size: 12px; color: #1f2937;">{{ $event->name }}</strong></td>
                <td style="text-align: right; vertical-align: middle;"><span class="event-status status-ongoing" style="padding: 2px 7px; font-size: 9px; font-weight: bold; text-transform: uppercase;">{{ $event->status }}</span></td>
            </tr></table>
            <table class="event-details-tbl"><tr>
                <td><span class="detail-label">Date:</span> {{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</td>
                <td><span class="detail-label">Venue:</span> {{ $event->venue }}</td>
            </tr><tr>
                <td><span class="detail-label">Calamity:</span> {{ $event->calamity->name ?? 'N/A' }}</td>
                <td><span class="detail-label">Beneficiaries:</span> {{ $event->eventBarangays->sum(function($eb) { return $eb->beneficiary->count(); }) }}</td>
            </tr></table>
            <div class="barangays"><strong>Coverage:</strong> @foreach($event->eventBarangays as $eb){{ $eb->barangay->name }}, {{ $eb->municipality->name }}@if(!$loop->last); @endif@endforeach</div>
        </div>
        @endforeach
    </div>
    @endif

    @if($events->where('status', 'Upcoming')->count())
    <div class="section">
        <div class="section-title upcoming">Upcoming Events</div>
        @foreach($events->where('status', 'Upcoming') as $event)
        <div class="event-card upcoming">
            <table class="event-name-tbl"><tr>
                <td style="vertical-align: middle;"><strong style="font-size: 12px; color: #1f2937;">{{ $event->name }}</strong></td>
                <td style="text-align: right; vertical-align: middle;"><span class="event-status status-upcoming" style="padding: 2px 7px; font-size: 9px; font-weight: bold; text-transform: uppercase;">{{ $event->status }}</span></td>
            </tr></table>
            <table class="event-details-tbl"><tr>
                <td><span class="detail-label">Date:</span> {{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</td>
                <td><span class="detail-label">Venue:</span> {{ $event->venue }}</td>
            </tr><tr>
                <td><span class="detail-label">Calamity:</span> {{ $event->calamity->name ?? 'N/A' }}</td>
                <td><span class="detail-label">Beneficiaries:</span> {{ $event->eventBarangays->sum(function($eb) { return $eb->beneficiary->count(); }) }}</td>
            </tr></table>
            <div class="barangays"><strong>Coverage:</strong> @foreach($event->eventBarangays as $eb){{ $eb->barangay->name }}, {{ $eb->municipality->name }}@if(!$loop->last); @endif@endforeach</div>
        </div>
        @endforeach
    </div>
    @endif

    @if($events->where('status', 'Done')->count())
    <div class="section">
        <div class="section-title done">Completed Events</div>
        @foreach($events->where('status', 'Done') as $event)
        <div class="event-card done">
            <table class="event-name-tbl"><tr>
                <td style="vertical-align: middle;"><strong style="font-size: 12px; color: #1f2937;">{{ $event->name }}</strong></td>
                <td style="text-align: right; vertical-align: middle;"><span class="event-status status-done" style="padding: 2px 7px; font-size: 9px; font-weight: bold; text-transform: uppercase;">{{ $event->status }}</span></td>
            </tr></table>
            <table class="event-details-tbl"><tr>
                <td><span class="detail-label">Date:</span> {{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</td>
                <td><span class="detail-label">Venue:</span> {{ $event->venue }}</td>
            </tr><tr>
                <td><span class="detail-label">Calamity:</span> {{ $event->calamity->name ?? 'N/A' }}</td>
                <td><span class="detail-label">Beneficiaries:</span> {{ $event->eventBarangays->sum(function($eb) { return $eb->beneficiary->count(); }) }}</td>
            </tr></table>
            <div class="barangays"><strong>Coverage:</strong> @foreach($event->eventBarangays as $eb){{ $eb->barangay->name }}, {{ $eb->municipality->name }}@if(!$loop->last); @endif@endforeach</div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        <strong style="color: #6b7280; font-size: 10px;">SPUP-CDC Disaster Response System</strong><br>
        Relief Monitor Report &bull; This is a system-generated document &bull; {{ now()->format('F d, Y H:i:s') }}
    </div>
</body>
</html>
