<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Beneficiaries Report</title>
    <style>
        * { margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #1f2937; line-height: 1.5; margin: 25px; }
        .top-bar { background-color: #1a3d1f; height: 5px; margin-bottom: 16px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px solid #dee2e6; }
        .org-name { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
        .doc-title { font-size: 20px; font-weight: bold; color: #1a3d1f; }
        .gen-date { font-size: 10px; color: #9ca3af; margin-top: 3px; }
        .report-badge { background-color: #1a3d1f; color: white; padding: 5px 12px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .stats-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .stats-table td { padding: 5px; vertical-align: top; }
        .stat-card { text-align: center; background-color: #f0f7f0; border: 1px solid #c9d7c9; border-top: 3px solid #1a3d1f; padding: 12px 8px; }
        .stat-number { font-size: 22px; font-weight: bold; color: #1a3d1f; }
        .stat-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.3px; margin-top: 3px; }
        .section-title { font-size: 11px; font-weight: bold; color: #1a3d1f; background-color: #f0f7f0; border-left: 4px solid #1a3d1f; padding: 6px 10px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.3px; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .data-table th { background-color: #1a3d1f; color: #fff; padding: 8px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.3px; font-weight: bold; border: 1px solid #1a3d1f; }
        .data-table td { padding: 7px 8px; border: 1px solid #e5e7eb; color: #374151; vertical-align: middle; }
        .data-table .row-alt td { background-color: #f7faf7; }
        .badge { display: inline-block; padding: 2px 6px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; }
        .badge-verified { background-color: #d1fae5; color: #065f46; }
        .badge-pending { background-color: #fef3c7; color: #92400e; }
        .badge-good { background-color: #d1fae5; color: #065f46; }
        .badge-poor { background-color: #fee2e2; color: #991b1b; }
        .page-footer { margin-top: 30px; padding-top: 10px; border-top: 2px solid #dee2e6; text-align: center; color: #9ca3af; font-size: 9px; }
        .no-data { text-align: center; color: #9ca3af; font-style: italic; padding: 20px; background-color: #f9fafb; border: 1px dashed #d1d5db; }
    </style>
</head>
<body>
    <div class="top-bar"></div>
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="org-name">SPUP-CDC Disaster Response System</div>
                <div class="doc-title">Beneficiaries Report</div>
                <div class="gen-date">Generated: {{ now()->format('F d, Y h:i A') }}</div>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <span class="report-badge">Official Report</span>
            </td>
        </tr>
    </table>

    <table class="stats-table">
        <tr>
            <td style="width: 25%;"><div class="stat-card"><div class="stat-number">{{ $beneficiaries->count() }}</div><div class="stat-label">Total Beneficiaries</div></div></td>
            <td style="width: 25%;"><div class="stat-card"><div class="stat-number">{{ $beneficiaries->where('is_verified', true)->count() }}</div><div class="stat-label">Verified</div></div></td>
            <td style="width: 25%;"><div class="stat-card"><div class="stat-number">{{ $beneficiaries->where('is_verified', false)->count() }}</div><div class="stat-label">Pending</div></div></td>
            <td style="width: 25%;"><div class="stat-card"><div class="stat-number">{{ $beneficiaries->avg('family_size') ? number_format($beneficiaries->avg('family_size'), 1) : '0' }}</div><div class="stat-label">Avg Family Size</div></div></td>
        </tr>
    </table>

    <div class="section-title">Beneficiary List</div>

    @if($beneficiaries->isNotEmpty())
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%; text-align: center;">#</th>
                    <th style="width: 22%;">Full Name</th>
                    <th style="width: 16%;">Barangay</th>
                    <th style="width: 10%; text-align: center;">Family Size</th>
                    <th style="width: 14%;">Monthly Income</th>
                    <th style="width: 10%; text-align: center;">Criteria</th>
                    <th style="width: 14%;">Vulnerability</th>
                    <th style="width: 10%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($beneficiaries as $i => $beneficiary)
                <tr class="{{ $loop->even ? 'row-alt' : '' }}">
                    <td style="text-align: center; color: #9ca3af;">{{ $i + 1 }}</td>
                    <td><strong>{{ $beneficiary->last_name }}, {{ $beneficiary->first_name }}</strong></td>
                    <td>{{ $beneficiary->barangay->name ?? 'N/A' }}</td>
                    <td style="text-align: center;">{{ $beneficiary->family_size }}</td>
                    <td>&#8369;{{ number_format($beneficiary->monthly_income, 0) }}</td>
                    <td style="text-align: center;">
                        <span class="badge {{ $beneficiary->criteria_met >= 3 ? 'badge-good' : 'badge-poor' }}">{{ $beneficiary->criteria_met }}/5</span>
                    </td>
                    <td>{{ $beneficiary->vulnerability_level ?? 'N/A' }}</td>
                    <td>
                        @if($beneficiary->is_verified)
                            <span class="badge badge-verified">Verified</span>
                        @else
                            <span class="badge badge-pending">Pending</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">No beneficiaries found matching the selected criteria.</div>
    @endif

    <div class="page-footer">
        <strong style="color: #6b7280; font-size: 10px;">SPUP-CDC Disaster Response System</strong><br>
        This is a system-generated document &bull; {{ now()->format('F d, Y h:i A') }}
    </div>
</body>
</html>
