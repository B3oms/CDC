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
            background: #ffffff;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #1a3d1f;
            padding-bottom: 25px;
            margin-bottom: 30px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1a3d1f;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header .event-name {
            font-size: 20px;
            color: #374151;
            margin: 8px 0;
            font-weight: 600;
        }
        .header .generated {
            margin: 5px 0 0 0;
            color: #6b7280;
            font-size: 12px;
        }
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .summary-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .summary-card .value {
            font-size: 24px;
            font-weight: 700;
            color: #1a3d1f;
            margin-bottom: 5px;
        }
        .summary-card .label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .event-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .info-item {
            display: flex;
            align-items: flex-start;
        }
        .info-icon {
            width: 24px;
            height: 24px;
            background: #1a3d1f;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
            font-size: 12px;
        }
        .info-content {
            flex: 1;
        }
        .info-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .info-value {
            color: #2c2c2a;
            font-size: 14px;
            font-weight: 500;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 25px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-ongoing {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
        }
        .status-upcoming {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);
        }
        .status-done {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(107, 114, 128, 0.3);
        }
        .section {
            margin-bottom: 35px;
        }
        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #1a3d1f;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
        }
        .section-title::before {
            content: '';
            width: 4px;
            height: 20px;
            background: #1a3d1f;
            margin-right: 12px;
            border-radius: 2px;
        }
        .facilitator-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .facilitator-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 15px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .facilitator-avatar {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .facilitator-info {
            flex: 1;
        }
        .facilitator-name {
            font-weight: 600;
            color: #2c2c2a;
            font-size: 14px;
            margin-bottom: 3px;
        }
        .facilitator-role {
            color: #6b7280;
            font-size: 12px;
        }
        .barangay-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .barangay-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .barangay-header {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .barangay-name {
            font-weight: 700;
            font-size: 16px;
            color: #1a3d1f;
            margin: 0;
        }
        .municipality {
            color: #6b7280;
            font-size: 12px;
            margin-top: 3px;
            font-weight: 500;
        }
        .barangay-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        .barangay-stat {
            text-align: center;
            padding: 10px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .barangay-stat .value {
            font-size: 18px;
            font-weight: 700;
            color: #1a3d1f;
            margin-bottom: 3px;
        }
        .barangay-stat .label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .beneficiary-section {
            margin-top: 15px;
        }
        .beneficiary-header {
            font-weight: 600;
            color: #1a3d1f;
            font-size: 12px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .beneficiary-list {
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
        }
        .beneficiary-item {
            padding: 6px 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        .beneficiary-item:last-child {
            border-bottom: none;
        }
        .beneficiary-name {
            font-weight: 500;
            color: #2c2c2a;
        }
        .beneficiary-contact {
            color: #6b7280;
            font-size: 10px;
        }
        .no-data {
            color: #6b7280;
            font-style: italic;
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px dashed #d1d5db;
        }
        .footer {
            margin-top: 50px;
            padding: 25px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
        }
        .footer .system-name {
            font-weight: 600;
            color: #1a3d1f;
            font-size: 12px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relief Event Report</h1>
        <div class="event-name">{{ $event->name }}</div>
        <div class="generated">Generated on {{ now()->format('F d, Y H:i:s') }}</div>
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <div class="value">{{ $event->eventBarangays->count() }}</div>
            <div class="label">Coverage Areas</div>
        </div>
        <div class="summary-card">
            <div class="value">{{ $event->beneficiaries->count() }}</div>
            <div class="label">Total Beneficiaries</div>
        </div>
        <div class="summary-card">
            <div class="value">{{ $event->facilitators->count() }}</div>
            <div class="label">Facilitators</div>
        </div>
        <div class="summary-card">
            <div class="value">{{ $event->status }}</div>
            <div class="label">Status</div>
        </div>
    </div>

    <div class="event-info">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-icon">📅</div>
                <div class="info-content">
                    <div class="info-label">Event Date</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}</div>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">📍</div>
                <div class="info-content">
                    <div class="info-label">Venue</div>
                    <div class="info-value">{{ $event->venue }}</div>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">🌪️</div>
                <div class="info-content">
                    <div class="info-label">Calamity</div>
                    <div class="info-value">{{ $event->calamity ? $event->calamity->name : 'No Calamity' }}</div>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">👤</div>
                <div class="info-content">
                    <div class="info-label">Created By</div>
                    <div class="info-value">{{ $event->creator ? $event->creator->first_name . ' ' . $event->creator->last_name : 'Unknown' }}</div>
                </div>
            </div>
        </div>
        <div style="margin-top: 20px; text-align: center;">
            <span class="status-badge status-{{ strtolower($event->status) }}">{{ $event->status }}</span>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">Facilitators</h2>
        @if($event->facilitators->count() > 0)
            <div class="facilitator-grid">
                @foreach($event->facilitators as $facilitator)
                    <div class="facilitator-card">
                        <div class="facilitator-avatar">
                            {{ strtoupper(substr($facilitator->first_name,0,1).substr($facilitator->last_name,0,1)) }}
                        </div>
                        <div class="facilitator-info">
                            <div class="facilitator-name">{{ $facilitator->first_name }} {{ $facilitator->last_name }}</div>
                            <div class="facilitator-role">{{ $facilitator->role->name }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-data">No facilitators assigned to this event</div>
        @endif
    </div>

    <div class="section">
        <h2 class="section-title">Coverage Areas & Beneficiaries</h2>
        @if($event->eventBarangays->count() > 0)
            <div class="barangay-grid">
                @foreach($event->eventBarangays as $eventBarangay)
                    <div class="barangay-card">
                        <div class="barangay-header">
                            <h3 class="barangay-name">{{ $eventBarangay->barangay->name }}</h3>
                            <div class="municipality">{{ $eventBarangay->municipality->name }}</div>
                        </div>
                        
                        <div class="barangay-stats">
                            <div class="barangay-stat">
                                <div class="value">{{ $event->beneficiaries->where('barangay_id', $eventBarangay->barangay_id)->count() }}</div>
                                <div class="label">Beneficiaries</div>
                            </div>
                            <div class="barangay-stat">
                                <div class="value">{{ $eventBarangay->target_beneficiaries ?? 'N/A' }}</div>
                                <div class="label">Target</div>
                            </div>
                            <div class="barangay-stat">
                                <div class="value">{{ $eventBarangay->actual_beneficiaries ?? 'N/A' }}</div>
                                <div class="label">Actual</div>
                            </div>
                        </div>

                        @php $barangayBeneficiaries = $event->beneficiaries->where('barangay_id', $eventBarangay->barangay_id); @endphp
                        @if($barangayBeneficiaries->count() > 0)
                            <div class="beneficiary-section">
                                <div class="beneficiary-header">Beneficiary List ({{ $barangayBeneficiaries->count() }})</div>
                                <div class="beneficiary-list">
                                    @foreach($barangayBeneficiaries as $beneficiary)
                                        <div class="beneficiary-item">
                                            <span class="beneficiary-name">{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</span>
                                            @if($beneficiary->contact_number)
                                                <span class="beneficiary-contact">• {{ $beneficiary->contact_number }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="no-data">No beneficiaries recorded for this area</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-data">No coverage areas assigned to this event</div>
        @endif
    </div>

    <div class="footer">
        <div class="system-name">Relief Monitoring and Distribution System</div>
        <div>Event Report - Generated on {{ now()->format('F d, Y H:i:s') }}</div>
        <div style="margin-top: 8px; font-size: 9px;">This is an official system-generated document</div>
    </div>
</body>
</html>
