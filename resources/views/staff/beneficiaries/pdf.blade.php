<!DOCTYPE html>
<html>
<head>
    <title>Beneficiaries List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
        }
        .header p {
            margin: 5px 0;
            color: #666;
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
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .verified {
            color: #28a745;
            font-weight: bold;
        }
        .pending {
            color: #ffc107;
            font-weight: bold;
        }
        .criteria-met {
            font-weight: bold;
        }
        .criteria-good {
            color: #28a745;
        }
        .criteria-bad {
            color: #dc3545;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Beneficiaries List</h1>
        <p>Generated on: {{ now()->format('F d, Y h:i A') }}</p>
        <p>Total Beneficiaries: {{ $beneficiaries->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Barangay</th>
                <th>Family Size</th>
                <th>Monthly Income</th>
                <th>Criteria Met</th>
                <th>Vulnerability</th>
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
                    <span class="criteria-met {{ $beneficiary->criteria_met >= 2 ? 'criteria-good' : 'criteria-bad' }}">
                        {{ $beneficiary->criteria_met }}/4
                    </span>
                </td>
                <td>{{ $beneficiary->vulnerability_level }}</td>
                <td>
                    @if($beneficiary->is_verified)
                        <span class="verified">Verified</span>
                    @else
                        <span class="pending">Pending</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This document was generated automatically by SPUP-CDC Disaster Response System</p>
    </div>
</body>
</html>
