<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relief Event Report - {{ $event->name }}</title>
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
        .event-info {
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .info-row {
            display: flex;
            margin-bottom: 15px;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }
        .info-label {
            font-weight: 600;
            color: #6b7280;
            width: 120px;
            flex-shrink: 0;
        }
        .info-value {
            color: #2c2c2a;
            flex: 1;
        }
        .status-badge {
            display: inline-block;
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
        .barangay-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .barangay-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .barangay-name {
            font-weight: bold;
            font-size: 14px;
            color: #2c2c2a;
            margin: 0;
        }
        .municipality {
            color: #6b7280;
            font-size: 11px;
            margin-top: 2px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 10px;
        }
        .stat-item {
            text-align: center;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #1a3d1f;
        }
        .stat-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .beneficiary-list {
            margin-top: 10px;
            font-size: 11px;
        }
        .beneficiary-item {
            padding: 3px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .beneficiary-item:last-child {
            border-bottom: none;
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
        <h1>Relief Event Report</h1>
        <p>{{ $event->name }}</p>
        <p>Generated on {{ now()->format('F d, Y H:i:s') }}</p>
    </div>

    <div class="event-info">
        <div class="info-row">
            <div class="info-label">Event Name:</div>
            <div class="info-value">{{ $event->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                <span class="status-badge status-{{ strtolower($event->status) }}">{{ $event->status }}</span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Date:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Venue:</div>
            <div class="info-value">{{ $event->venue }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Calamity:</div>
            <div class="info-value">{{ $event->calamity ? $event->calamity->name : 'No Calamity' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Created By:</div>
            <div class="info-value">{{ $event->creator ? $event->creator->first_name . ' ' . $event->creator->last_name : 'Unknown' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Total Areas:</div>
            <div class="info-value">{{ $event->eventBarangays->count() }} barangays</div>
        </div>
        <div class="info-row">
            <div class="info-label">Total Beneficiaries:</div>
            <div class="info-value">{{ $event->beneficiaries->count() }}</div>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">Facilitators</h2>
        @if($event->facilitators->count() > 0)
            <div style="margin-bottom: 1rem;">
                @foreach($event->facilitators as $facilitator)
                    <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                        <div style="background: #3b82f6; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 0.75rem;">
                            {{ strtoupper(substr($facilitator->first_name,0,1).substr($facilitator->last_name,0,1)) }}
                        </div>
                        <div>
                            <div style="font-weight: 600;">{{ $facilitator->first_name }} {{ $facilitator->last_name }}</div>
                            <div style="font-size: 12px; color: #6b7280;">{{ $facilitator->role->name }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="color: #6b7280; font-style: italic;">No facilitators assigned</div>
        @endif
    </div>

    <div class="section">
        <h2 class="section-title">Coverage Areas & Beneficiaries</h2>
        @foreach($event->eventBarangays as $eventBarangay)
        <div class="barangay-card">
            <div class="barangay-header">
                <div>
                    <h3 class="barangay-name">{{ $eventBarangay->barangay->name }}</h3>
                    <div class="municipality">{{ $eventBarangay->municipality->name }}</div>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value">{{ $event->beneficiaries->where('barangay_id', $eventBarangay->barangay_id)->count() }}</div>
                    <div class="stat-label">Beneficiaries</div>
                </div>
            </div>

            @php $barangayBeneficiaries = $event->beneficiaries->where('barangay_id', $eventBarangay->barangay_id); @endphp
            @if($barangayBeneficiaries->count() > 0)
            <div class="beneficiary-list">
                <strong>Beneficiaries:</strong>
                @foreach($barangayBeneficiaries as $beneficiary)
                <div class="beneficiary-item">
                    {{ $beneficiary->first_name }} {{ $beneficiary->last_name }}
                    @if($beneficiary->contact_number)
                        ({{ $beneficiary->contact_number }})
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div style="color: #6b7280; font-size: 11px; font-style: italic;">
                No beneficiaries recorded for this area
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="footer">
        <p>Relief Monitoring and Distribution System - Event Report</p>
        <p>This report was automatically generated on {{ now()->format('F d, Y H:i:s') }}</p>
    </div>
</body>
</html>
