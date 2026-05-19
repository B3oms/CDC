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
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            margin: 0;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .category-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .category-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
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
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .status-low {
            color: #dc3545;
            font-weight: bold;
        }
        .status-normal {
            color: #28a745;
        }
        .status-expired {
            color: #6c757d;
            font-style: italic;
        }
        .summary-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        .summary-item {
            text-align: center;
            padding: 10px;
            background-color: #fff;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .summary-number {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        .summary-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVENTORY REPORT</h1>
        <p>SPUP-CDC Disaster Response System</p>
        <p>Generated: {{ $generated_date }}</p>
    </div>

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
        <h3>Inventory Summary</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-number">{{ $totalCategories }}</div>
                <div class="summary-label">Total Categories</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $totalSubcategories }}</div>
                <div class="summary-label">Total Subcategories</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $totalItems }}</div>
                <div class="summary-label">Total Items</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $lowStockItems }}</div>
                <div class="summary-label">Low Stock Items</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $expiringItems }}</div>
                <div class="summary-label">Expiring Items (30 days)</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $totalQuantity }}</div>
                <div class="summary-label">Total Quantity</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This report was generated automatically by the SPUP-CDC Disaster Response System</p>
        <p>For questions or concerns, please contact the CDC office</p>
    </div>
</body>
</html>
