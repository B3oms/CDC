<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relief Event Report - {{ $event->name }}</title>
    <style>
        * { margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #1f2937; line-height: 1.5; margin: 25px; }
        .top-bar { background-color: #1a3d1f; height: 5px; margin-bottom: 16px; }
        .header-tbl { width: 100%; border-collapse: collapse; margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px solid #dee2e6; }
        .org-name { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
        .rpt-badge { background-color: #1a3d1f; color: white; padding: 5px 12px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .stats-tbl { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .stats-tbl td { padding: 5px; vertical-align: top; width: 25%; }
        .stat-card { text-align: center; background-color: #f0f7f0; border: 1px solid #c9d7c9; border-top: 3px solid #1a3d1f; padding: 12px 8px; }
        .stat-value { font-size: 20px; font-weight: bold; color: #1a3d1f; }
        .stat-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.3px; margin-top: 3px; }
        .section { margin-bottom: 22px; }
        .section-title { font-size: 11px; font-weight: bold; color: #1a3d1f; background-color: #f0f7f0; border-left: 4px solid #1a3d1f; padding: 6px 10px; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.3px; }
        .event-info { background-color: #f9fafb; border: 1px solid #e5e7eb; border-left: 4px solid #1a3d1f; padding: 15px; margin-bottom: 20px; }
        .info-grid-tbl { width: 100%; border-collapse: collapse; }
        .info-grid-tbl td { padding: 5px 6px; vertical-align: top; width: 50%; }
        .info-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; font-weight: 600; }
        .info-value { font-size: 12px; color: #1f2937; font-weight: 500; }
        .status-badge { display: inline-block; padding: 3px 10px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; }
        .status-ongoing { background-color: #d1fae5; color: #065f46; }
        .status-upcoming { background-color: #fef3c7; color: #92400e; }
        .status-done { background-color: #f3f4f6; color: #374151; }
        .facilitator-tbl { width: 100%; border-collapse: collapse; }
        .facilitator-tbl td { padding: 5px; vertical-align: top; width: 50%; }
        .facilitator-card { background-color: #f9fafb; border: 1px solid #e5e7eb; border-left: 3px solid #1a3d1f; padding: 10px; }
        .facilitator-name { font-weight: 600; color: #1f2937; font-size: 12px; margin-bottom: 2px; }
        .facilitator-role { color: #6b7280; font-size: 10px; }
        .barangay-tbl { width: 100%; border-collapse: collapse; }
        .barangay-tbl td { padding: 5px; vertical-align: top; width: 50%; }
        .barangay-card { background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 12px; }
        .barangay-name { font-weight: bold; font-size: 13px; color: #1a3d1f; margin-bottom: 2px; }
        .municipality { color: #6b7280; font-size: 10px; margin-bottom: 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; }
        .bstats-tbl { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .bstats-tbl td { padding: 3px; text-align: center; vertical-align: top; width: 33%; }
        .bstat-cell { background-color: white; border: 1px solid #d1d5db; padding: 5px 3px; }
        .bstat-value { font-size: 14px; font-weight: bold; color: #1a3d1f; }
        .bstat-label { font-size: 8px; color: #6b7280; text-transform: uppercase; }
        .beneficiary-header { font-weight: 600; color: #1a3d1f; font-size: 10px; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.3px; }
        .beneficiary-list { background-color: #f8f9fa; border: 1px solid #e5e7eb; padding: 8px; }
        .beneficiary-item { padding: 3px 0; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        .beneficiary-name { font-weight: 500; color: #1f2937; }
        .beneficiary-contact { color: #6b7280; font-size: 9px; }
        .no-data { color: #9ca3af; font-style: italic; text-align: center; padding: 12px; background-color: #f9fafb; border: 1px dashed #d1d5db; font-size: 10px; }
        .footer { margin-top: 30px; padding-top: 10px; border-top: 2px solid #dee2e6; text-align: center; color: #9ca3af; font-size: 9px; }
    </style>
</head>
<body>
    <div class="top-bar"></div>
    <table class="header-tbl">
        <tr>
            <td style="vertical-align: middle;">
                <div class="org-name">SPUP-CDC Disaster Response System</div>
                <div style="font-size: 19px; font-weight: bold; color: #1a3d1f;">{{ $event->name }}</div>
                <div style="font-size: 10px; color: #9ca3af; margin-top: 2px;">Relief Event Report &bull; Generated: {{ now()->format('F d, Y H:i:s') }}</div>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <span class="rpt-badge">Official Report</span>
            </td>
        </tr>
    </table>

    <table class="stats-tbl">
        <tr>
            <td><div class="stat-card"><div class="stat-value">{{ $event->eventBarangays->count() }}</div><div class="stat-label">Coverage Areas</div></div></td>
            <td><div class="stat-card"><div class="stat-value">{{ $event->beneficiaries->count() }}</div><div class="stat-label">Total Beneficiaries</div></div></td>
            <td><div class="stat-card"><div class="stat-value">{{ $event->facilitators->count() }}</div><div class="stat-label">Facilitators</div></div></td>
            <td><div class="stat-card"><div class="stat-value"><span class="status-badge status-{{ strtolower($event->status) }}">{{ $event->status }}</span></div><div class="stat-label">Status</div></div></td>
        </tr>
    </table>

    <div class="event-info">
        <table class="info-grid-tbl">
            <tr>
                <td><div class="info-label">Event Date</div><div class="info-value">{{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}</div></td>
                <td><div class="info-label">Venue</div><div class="info-value">{{ $event->venue }}</div></td>
            </tr>
            <tr>
                <td><div class="info-label">Calamity</div><div class="info-value">{{ $event->calamity ? $event->calamity->name : 'No Calamity' }}</div></td>
                <td><div class="info-label">Created By</div><div class="info-value">{{ $event->creator ? $event->creator->first_name . ' ' . $event->creator->last_name : 'Unknown' }}</div></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Facilitators</h2>
        @if($event->facilitators->count() > 0)
            <table class="facilitator-tbl">
                @foreach($event->facilitators->chunk(2) as $chunk)
                <tr>
                    @foreach($chunk as $facilitator)
                    <td><div class="facilitator-card"><div class="facilitator-name">{{ $facilitator->first_name }} {{ $facilitator->last_name }}</div><div class="facilitator-role">{{ $facilitator->role->name }}</div></div></td>
                    @endforeach
                    @if($chunk->count() < 2)<td></td>@endif
                </tr>
                @endforeach
            </table>
        @else
            <div class="no-data">No facilitators assigned to this event</div>
        @endif
    </div>

    <div class="section">
        <h2 class="section-title">Coverage Areas & Beneficiaries</h2>
        @if($event->eventBarangays->count() > 0)
            <table class="barangay-tbl">
                @foreach($event->eventBarangays->chunk(2) as $chunk)
                <tr>
                    @foreach($chunk as $eventBarangay)
                    <td>
                        <div class="barangay-card">
                            <div class="barangay-name">{{ $eventBarangay->barangay->name }}</div>
                            <div class="municipality">{{ $eventBarangay->municipality->name }}</div>
                            <table class="bstats-tbl">
                                <tr>
                                    <td><div class="bstat-cell"><div class="bstat-value">{{ $event->beneficiaries->where('barangay_id', $eventBarangay->barangay_id)->count() }}</div><div class="bstat-label">Beneficiaries</div></div></td>
                                    <td><div class="bstat-cell"><div class="bstat-value">{{ $eventBarangay->target_beneficiaries ?? 'N/A' }}</div><div class="bstat-label">Target</div></div></td>
                                    <td><div class="bstat-cell"><div class="bstat-value">{{ $eventBarangay->actual_beneficiaries ?? 'N/A' }}</div><div class="bstat-label">Actual</div></div></td>
                                </tr>
                            </table>
                            @php $barangayBeneficiaries = $event->beneficiaries->where('barangay_id', $eventBarangay->barangay_id); @endphp
                            @if($barangayBeneficiaries->count() > 0)
                            <div>
                                <div class="beneficiary-header">Beneficiary List ({{ $barangayBeneficiaries->count() }})</div>
                                <div class="beneficiary-list">
                                    @foreach($barangayBeneficiaries as $reliefEventBeneficiary)
                                        @php $beneficiary = $reliefEventBeneficiary->beneficiary @endphp
                                        @if($beneficiary)
                                        <div class="beneficiary-item">
                                            <span class="beneficiary-name">{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</span>
                                            @if($beneficiary->contact_number)<span class="beneficiary-contact"> &bull; {{ $beneficiary->contact_number }}</span>@endif
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @else
                            <div class="no-data">No beneficiaries recorded for this area</div>
                            @endif
                        </div>
                    </td>
                    @endforeach
                    @if($chunk->count() < 2)<td></td>@endif
                </tr>
                @endforeach
            </table>
        @else
            <div class="no-data">No coverage areas assigned to this event</div>
        @endif
    </div>

    <div class="footer">
        <strong style="color: #6b7280; font-size: 10px;">SPUP-CDC Disaster Response System</strong><br>
        Relief Event Report &bull; This is a system-generated document &bull; {{ now()->format('F d, Y H:i:s') }}
    </div>
</body>
</html>
