<!DOCTYPE html>
<html>
<head>
    <title>Inventory Report by Category</title>
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
        .category-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .category-title {
            font-size: 16px;
            font-weight: bold;
            color: #1a3d1f;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .subcategory-section {
            margin-bottom: 20px;
            margin-left: 20px;
        }
        .subcategory-title {
            font-size: 14px;
            font-weight: bold;
            color: #34495e;
            margin-bottom: 10px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background: white;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        .items-table th {
            background-color: #1a3d1f;
            color: white;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .status-low {
            background-color: #fee2e2;
            color: #dc2626;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-normal {
            background-color: #d1fae5;
            color: #059669;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-expired {
            background-color: #f3f4f6;
            color: #6b7280;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            font-style: italic;
        }
        .summary-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #f0f7f0;
            border: 1px solid #c9d7c9;
            border-top: 3px solid #1a3d1f;
        }
        .summary-grid-table { width: 100%; border-collapse: collapse; }
        .summary-grid-table td { padding: 5px; vertical-align: top; }
        .summary-item {
            text-align: center;
            padding: 10px 8px;
            background-color: white;
            border: 1px solid #d1d5db;
        }
        .summary-number {
            font-size: 20px;
            font-weight: bold;
            color: #1a3d1f;
        }
        .summary-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-top: 4px;
        }
        .top-bar { background-color: #1a3d1f; height: 5px; margin-bottom: 16px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px solid #dee2e6; }
        .org-name { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
        .report-badge { background-color: #1a3d1f; color: white; padding: 5px 12px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-grid-table { width: 100%; border-collapse: collapse; }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #9ca3af;
            font-size: 9px;
            border-top: 2px solid #dee2e6;
            padding-top: 10px;
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
                <div style="font-size: 20px; font-weight: bold; color: #1a3d1f;">INVENTORY REPORT</div>
                <div style="font-size: 10px; color: #9ca3af; margin-top: 3px;">Generated: {{ $generated_date }}</div>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <span class="report-badge">Official Report</span>
            </td>
        </tr>
    </table>

    @foreach($categories as $category)
    <div class="category-section">
        <div class="category-title">{{ strtoupper($category->name) }}</div>
        
        @foreach($category->subcategories as $subcategory)
        <div class="subcategory-section">
            <div class="subcategory-title">{{ $subcategory->name }}</div>
            
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>Expiration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subcategory->items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->description ?? 'N/A' }}</td>
                        <td>{{ $item->inventory ? $item->inventory->quantity : 0 }}</td>
                        <td>{{ $item->unit ?? 'pcs' }}</td>
                        <td>
                            @if($item->inventory)
                                @if($item->inventory->quantity <= 10)
                                    <span class="status-low">LOW STOCK</span>
                                @else
                                    <span class="status-normal">OK</span>
                                @endif
                            @else
                                <span class="status-low">NO STOCK</span>
                            @endif
                        </td>
                        <td>
                            @if($item->inventory && $item->inventory->expiration_date)
                                @if(\Carbon\Carbon::parse($item->inventory->expiration_date)->isPast())
                                    <span class="status-expired">EXPIRED</span>
                                @else
                                    {{ \Carbon\Carbon::parse($item->inventory->expiration_date)->format('M d, Y') }}
                                @endif
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
    </div>
    @endforeach

    <div class="summary-section">
        <div style="font-size: 12px; font-weight: bold; color: #1a3d1f; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.3px;">Inventory Summary</div>
        <table class="summary-grid-table">
            <tr>
                <td style="width: 33%;"><div class="summary-item"><div class="summary-number">{{ $totalCategories }}</div><div class="summary-label">Total Categories</div></div></td>
                <td style="width: 33%;"><div class="summary-item"><div class="summary-number">{{ $totalSubcategories }}</div><div class="summary-label">Total Subcategories</div></div></td>
                <td style="width: 33%;"><div class="summary-item"><div class="summary-number">{{ $totalItems }}</div><div class="summary-label">Total Items</div></div></td>
            </tr>
            <tr>
                <td><div class="summary-item"><div class="summary-number" style="color: #dc2626;">{{ $lowStockItems }}</div><div class="summary-label">Low Stock Items</div></div></td>
                <td><div class="summary-item"><div class="summary-number" style="color: #d97706;">{{ $expiringItems }}</div><div class="summary-label">Expiring Items (30 days)</div></div></td>
                <td><div class="summary-item"><div class="summary-number">{{ $totalQuantity }}</div><div class="summary-label">Total Quantity</div></div></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <strong style="color: #6b7280; font-size: 10px;">SPUP-CDC Disaster Response System</strong><br>
        This is a system-generated document &bull; {{ $generated_date }}
    </div>
</body>
</html>
