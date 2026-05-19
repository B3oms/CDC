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
            margin-bottom: 10px;
            padding: 8px;
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
        }
        .category-description {
            font-size: 11px;
            color: #666;
            margin-bottom: 15px;
            font-style: italic;
        }
        .subcategory-section {
            margin-bottom: 20px;
            margin-left: 20px;
        }
        .subcategory-title {
            font-size: 14px;
            font-weight: bold;
            color: #34495e;
            margin-bottom: 8px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }
        .items-table th {
            background-color: #ecf0f1;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        .items-table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }
        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .quantity-low {
            color: #e74c3c;
            font-weight: bold;
        }
        .quantity-medium {
            color: #f39c12;
            font-weight: bold;
        }
        .quantity-good {
            color: #27ae60;
            font-weight: bold;
        }
        .no-items {
            font-style: italic;
            color: #999;
            text-align: center;
            padding: 10px;
        }
        .summary-section {
            margin-top: 40px;
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
        .summary-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        .summary-item {
            text-align: center;
            padding: 10px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .summary-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVENTORY REPORT</h1>
        <p>Organized by Category</p>
        <p>Generated on: {{ $generated_date }}</p>
    </div>

    @php
        $totalCategories = 0;
        $totalSubcategories = 0;
        $totalItems = 0;
        $lowStockCount = 0;
        $outOfStockCount = 0;
        $wellStockedCount = 0;
    @endphp

    @foreach($categories as $category)
        @php($totalCategories++) ?>
        <div class="category-section">
            <div class="category-title">
                {{ strtoupper($category->name) }}
                <span style="float: right; font-size: 12px; font-weight: normal;">
                    {{ $category->subcategories->count() }} Subcategories
                </span>
            </div>
            
            @if($category->description)
                <div class="category-description">{{ $category->description }}</div>
            @endif

            @if($category->subcategories->isEmpty())
                <div class="no-items">No subcategories found in this category.</div>
            @else
                @foreach($category->subcategories as $subcategory)
                    @php($totalSubcategories++) ?>
                    <div class="subcategory-section">
                        <div class="subcategory-title">
                            {{ $subcategory->name }}
                            <span style="float: right; font-size: 11px; font-weight: normal;">
                                {{ $subcategory->items->count() }} Items
                            </span>
                        </div>

                        @if($subcategory->items->isEmpty())
                            <div class="no-items">No items found in this subcategory.</div>
                        @else
                            <table class="items-table">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="25%">Item Name</th>
                                        <th width="15%">Quantity</th>
                                        <th width="15%">Unit</th>
                                        <th width="15%">Expiry Date</th>
                                        <th width="25%">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($itemIndex = 1)
                                    @foreach($subcategory->items as $item)
                                        @php($totalItems++) ?>
                                        <tr>
                                            <td>{{ $itemIndex++ }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>
                                                @if($item->inventory)
                                                    @if($item->inventory->quantity <= 10)
                                                        @php($lowStockCount++) ?>
                                                        <span class="quantity-low">{{ $item->inventory->quantity }}</span>
                                                    @elseif($item->inventory->quantity <= 50)
                                                        <span class="quantity-medium">{{ $item->inventory->quantity }}</span>
                                                    @else
                                                        @php($wellStockedCount++) ?>
                                                        <span class="quantity-good">{{ $item->inventory->quantity }}</span>
                                                    @endif
                                                @else
                                                    @php($outOfStockCount++) ?>
                                                    <span class="quantity-low">0</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->unit ?? 'N/A' }}</td>
                                            <td>
                                                @if($item->inventory && $item->inventory->expiry_date)
                                                    {{ \Carbon\Carbon::parse($item->inventory->expiry_date)->format('M d, Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->inventory)
                                                    @if($item->inventory->quantity <= 10)
                                                        <span style="color: #e74c3c; font-weight: bold;">⚠️ LOW STOCK</span>
                                                    @elseif($item->inventory->quantity <= 50)
                                                        <span style="color: #f39c12; font-weight: bold;">⚡ MEDIUM</span>
                                                    @else
                                                        <span style="color: #27ae60; font-weight: bold;">✓ IN STOCK</span>
                                                    @endif
                                                @else
                                                    <span style="color: #e74c3c; font-weight: bold;">❌ OUT OF STOCK</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    @endforeach

    <div class="summary-section">
        <div class="summary-title">INVENTORY SUMMARY</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Categories</div>
                <div class="summary-value">{{ $totalCategories }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Subcategories</div>
                <div class="summary-value">{{ $totalSubcategories }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Items</div>
                <div class="summary-value">{{ $totalItems }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Low Stock Items</div>
                <div class="summary-value">{{ $lowStockCount }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Out of Stock</div>
                <div class="summary-value">{{ $outOfStockCount }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Well Stocked</div>
                <div class="summary-value">{{ $wellStockedCount }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This report was automatically generated by the SPUP-CDC Relief System</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>
