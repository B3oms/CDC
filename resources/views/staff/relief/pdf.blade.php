<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $event->name }} - Relief Event Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
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
            padding: 10px;
            background: #f8f9fa;
            border-left: 4px solid #185fa5;
        }
        .info-item strong {
            color: #185fa5;
            display: block;
            margin-bottom: 5px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #185fa5;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #185fa5;
            color: white;
            font-weight: bold;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-ongoing {
            background-color: #d4edda;
            color: #155724;
        }
        .status-done {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-upcoming {
            background-color: #fff3cd;
            color: #856404;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 11px;
        }
        .no-data {
            text-align: center;
            color: #888;
            font-style: italic;
            padding: 20px;
        }
        .barangay-list {
            margin-bottom: 20px;
        }
        .barangay-item {
            padding: 8px;
            margin-bottom: 5px;
            background: #f8f9fa;
            border-radius: 4px;
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
                    <div style="font-size: 19px; font-weight: bold; color: #1a3d1f;">{{ $event->name }}</div>
                    <div style="font-size: 10px; color: #9ca3af; margin-top: 2px;">Relief Event Report &bull; Generated: {{ $generated_date }}</div>
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
                <td><div class="info-item"><strong>Event Type</strong>{{ $event->calamity->name ?? 'General Relief' }}</div></td>
                <td><div class="info-item"><strong>Date</strong>{{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}</div></td>
            </tr>
            <tr>
                <td><div class="info-item"><strong>Status</strong><span class="status-badge status-{{ strtolower($event->status) }}">{{ $event->status }}</span></div></td>
                <td><div class="info-item"><strong>Venue</strong>{{ $event->venue }}</div></td>
            </tr>
            <tr>
                <td><div class="info-item"><strong>Created by</strong>{{ $event->creator->first_name }} {{ $event->creator->last_name }}</div></td>
                <td><div class="info-item"><strong>Created at</strong>{{ $event->created_at->format('M d, Y h:i A') }}</div></td>
            </tr>
        </table>
        @if($event->description)
        <div class="info-item" style="margin-bottom: 15px;">
            <strong>Description</strong>
            {{ $event->description }}
        </div>
        @endif
    </div>

    <div class="section-title">Partner Barangays</div>
    <div class="barangay-list">
        @if($event->eventBarangays->isNotEmpty())
            @foreach($event->eventBarangays as $eventBarangay)
                <div class="barangay-item">
                    <div>{{ $eventBarangay->barangay->name }}, {{ $eventBarangay->municipality->name }}</div>
                    <div style="font-size: 11px; color: #666;">
                        {{ $eventBarangay->beneficiary_count ?? 0 }} beneficiaries
                    </div>
                </div>
            @endforeach
        @else
            <div class="no-data">No barangays assigned</div>
        @endif
    </div>

    <div class="section-title">Distributed Items</div>
    @if($event->distributedItems->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Total Quantity</th>
                    <th>Per Beneficiary</th>
                    <th>Beneficiaries</th>
                </tr>
            </thead>
            <tbody>
                @foreach($event->distributedItems as $distributedItem)
                    <tr>
                        <td>{{ $distributedItem->item->name }}</td>
                        <td>{{ $distributedItem->total_quantity }} {{ $distributedItem->unit }}</td>
                        <td>{{ $distributedItem->per_beneficiary }} {{ $distributedItem->unit }}</td>
                        <td>{{ $distributedItem->beneficiaries_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">No items distributed yet</div>
    @endif

    <div class="section-title">Beneficiaries Served</div>
    @if($event->beneficiaries->isNotEmpty())
        @php
            // Group beneficiaries by barangay
            $beneficiariesByBarangay = $event->beneficiaries->groupBy('barangay_id');
        @endphp
        @foreach($event->eventBarangays as $eventBarangay)
            @php
                $barangayBeneficiaries = $beneficiariesByBarangay->get($eventBarangay->barangay_id, collect());
            @endphp
            @if($barangayBeneficiaries->isNotEmpty())
                <h4 style="margin: 20px 0 10px 0; color: #185fa5;">{{ $eventBarangay->barangay->name }}</h4>
                <table style="margin-bottom: 20px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Family Size</th>
                            <th>Vulnerability</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barangayBeneficiaries as $i => $beneficiary)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $beneficiary->beneficiary->first_name }} {{ $beneficiary->beneficiary->last_name }}</td>
                                <td>{{ $beneficiary->beneficiary->family_size }}</td>
                                <td>
                                    <span style="padding: 2px 6px; border-radius: 3px; font-size: 10px; background: #e9ecef;">
                                        {{ $beneficiary->beneficiary->vulnerability_level ?? 'Medium' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach
    @else
        <div class="no-data">No beneficiaries served</div>
    @endif

    <div class="footer">
        <strong style="color: #6b7280; font-size: 10px;">SPUP-CDC Disaster Response System</strong><br>
        Relief Monitoring Report &bull; This is a system-generated document
    </div>
</body>
</html>
