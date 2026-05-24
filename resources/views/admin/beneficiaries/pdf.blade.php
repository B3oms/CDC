<!DOCTYPE html>
<html>
<head>
    <title>Beneficiaries Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
            line-height: 1.4;
        }
        .top-bar { background-color: #1a3d1f; height: 5px; margin-bottom: 16px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px solid #dee2e6; }
        .org-name { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
        .report-badge { background-color: #1a3d1f; color: white; padding: 5px 12px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .header {
            margin-bottom: 0;
        }
        .header h1 {
            font-size: 20px;
            margin: 0;
            color: #1a3d1f;
            font-weight: 700;
        }
        .header p {
            margin: 3px 0 0 0;
            color: #9ca3af;
            font-size: 10px;
        }
        .summary-section {
            margin-bottom: 20px;
        }
        .summary-grid-table { width: 100%; border-collapse: collapse; }
        .summary-grid-table td { padding: 5px; vertical-align: top; }
        .summary-item {
            text-align: center;
            background-color: #f0f7f0;
            border: 1px solid #c9d7c9;
            border-top: 3px solid #1a3d1f;
            padding: 12px 8px;
        }
        .summary-number {
            font-size: 22px;
            font-weight: bold;
            color: #1a3d1f;
        }
        .summary-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
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
        .status-verified {
            background-color: #d1fae5;
            color: #059669;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
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
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="org-name">SPUP-CDC Disaster Response System</div>
                <h1 style="font-size: 20px; font-weight: bold; color: #1a3d1f; margin: 0;">BENEFICIARIES REPORT</h1>
                <p style="margin: 3px 0 0 0; color: #9ca3af; font-size: 10px;">Generated: {{ $generated_date ?? now()->format('F d, Y h:i A') }}</p>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <span class="report-badge">Official Report</span>
            </td>
        </tr>
    </table>

    <div class="summary-section">
        <table class="summary-grid-table">
            <tr>
                <td style="width: 25%;"><div class="summary-item"><div class="summary-number">{{ $beneficiaries->count() }}</div><div class="summary-label">Total Beneficiaries</div></div></td>
                <td style="width: 25%;"><div class="summary-item"><div class="summary-number">{{ $beneficiaries->where('is_verified', true)->count() }}</div><div class="summary-label">Verified</div></div></td>
                <td style="width: 25%;"><div class="summary-item"><div class="summary-number">{{ $beneficiaries->where('is_verified', false)->count() }}</div><div class="summary-label">Pending Verification</div></div></td>
                <td style="width: 25%;"><div class="summary-item"><div class="summary-number">{{ $beneficiaries->avg('family_size') ? number_format($beneficiaries->avg('family_size'), 1) : 0 }}</div><div class="summary-label">Avg Family Size</div></div></td>
            </tr>
        </table>
    </div>

    @if($beneficiaries->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Barangay</th>
                    <th>Family Size</th>
                    <th>Monthly Income</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($beneficiaries as $i => $beneficiary)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</td>
                    <td>{{ $beneficiary->barangay->name ?? 'N/A' }}</td>
                    <td>{{ $beneficiary->family_size }}</td>
                    <td>₱{{ number_format($beneficiary->monthly_income, 0) }}</td>
                    <td>
                        @if($beneficiary->is_verified)
                            <span class="status-verified">VERIFIED</span>
                        @else
                            <span class="status-pending">PENDING</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            No beneficiaries found in the system.
        </div>
    @endif

    <div class="footer">
        <p>This report was generated automatically by the SPUP-CDC Disaster Response System</p>
        <p>For questions or concerns, please contact the CDC office</p>
    </div>
</body>
</html>