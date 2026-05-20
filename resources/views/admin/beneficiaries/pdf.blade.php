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
    <div class="header">
        <h1>BENEFICIARIES REPORT</h1>
        <p>SPUP-CDC Disaster Response System</p>
        <p>Generated: {{ $generated_date ?? now()->format('F d, Y h:i A') }}</p>
    </div>

    <div class="summary-section">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-number">{{ $beneficiaries->count() }}</div>
                <div class="summary-label">Total Beneficiaries</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $beneficiaries->where('is_verified', true)->count() }}</div>
                <div class="summary-label">Verified</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $beneficiaries->where('is_verified', false)->count() }}</div>
                <div class="summary-label">Pending Verification</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $beneficiaries->avg('family_size') ? number_format($beneficiaries->avg('family_size'), 1) : 0 }}</div>
                <div class="summary-label">Avg Family Size</div>
            </div>
        </div>
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