<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 15px;
            color: #333;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #185fa5;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        .header h1 {
            color: #185fa5;
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 11px;
            margin-top: 5px;
        }
        
        .chart-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .chart-container {
            width: 100%;
            border: 1px solid #ddd;
            padding: 15px;
            background: #f9f9f9;
            margin-bottom: 15px;
        }
        
        .chart-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .chart-bars {
            width: 100%;
            height: 250px;
            position: relative;
            border: 1px solid #ccc;
            background: white;
        }
        
        .chart-bar {
            position: absolute;
            bottom: 30px;
            background: #185fa5;
            color: white;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
        }
        
        .chart-bar-value {
            position: absolute;
            top: -20px;
            font-size: 9px;
            font-weight: bold;
            color: #333;
            width: 100%;
            text-align: center;
        }
        
        .chart-bar-label {
            position: absolute;
            bottom: -25px;
            font-size: 9px;
            width: 100%;
            text-align: center;
            color: #333;
        }
        
        .interpretation-section {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 15px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .interpretation-section h2 {
            color: #185fa5;
            margin-top: 0;
            margin-bottom: 12px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .interpretation-item {
            margin-bottom: 12px;
        }
        
        .interpretation-item strong {
            color: #333;
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .interpretation-item p {
            margin: 0;
            padding: 8px;
            background: white;
            border-left: 3px solid #185fa5;
            font-size: 11px;
        }
        
        .recommendations {
            background: #e8f5e8;
            border: 1px solid #c3e6c3;
            padding: 12px;
            page-break-inside: avoid;
        }
        
        .recommendations h3 {
            color: #155724;
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .recommendations ul {
            margin: 0;
            padding-left: 15px;
        }
        
        .recommendations li {
            margin-bottom: 6px;
            color: #155724;
            font-size: 11px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10px;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: center;
        }
        
        .data-table th {
            background: #185fa5;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="subtitle">Generated on {{ now()->format('F d, Y H:i:s') }}</div>
    </div>

    <div class="chart-section">
        <div class="chart-title">Chart Visualization</div>
        <div class="chart-container">
            <!-- Simple Chart using Tables -->
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <tr>
                    <td style="text-align: center; padding: 10px;">
                        @php
                            $maxValue = max($chartData['values']);
                            $maxHeight = 120;
                            $barWidth = 35;
                        @endphp
                        
                        <!-- Chart Container -->
                        <div style="border: 1px solid #ccc; background: white; padding: 15px; margin-bottom: 10px;">
                            <!-- Values Row -->
                            <div style="margin-bottom: 5px;">
                                @foreach($chartData['labels'] as $index => $label)
                                    <span style="display: inline-block; width: {{ $barWidth }}px; text-align: center; font-size: 10px; font-weight: bold; margin: 0 3px;">{{ $chartData['values'][$index] }}</span>
                                @endforeach
                            </div>
                            
                            <!-- Bars Row -->
                            <div style="height: {{ $maxHeight }}px; border-bottom: 2px solid #333; position: relative;">
                                @foreach($chartData['labels'] as $index => $label)
                                    @php
                                        $value = $chartData['values'][$index];
                                        $height = $maxValue > 0 ? ($value / $maxValue) * $maxHeight : 0;
                                    @endphp
                                    <div style="display: inline-block; width: {{ $barWidth }}px; height: {{ $height }}px; background: #185fa5; border: 1px solid #0d3d7a; margin: 0 3px; vertical-align: bottom;"></div>
                                @endforeach
                            </div>
                            
                            <!-- Labels Row -->
                            <div style="margin-top: 5px;">
                                @foreach($chartData['labels'] as $index => $label)
                                    <span style="display: inline-block; width: {{ $barWidth }}px; text-align: center; font-size: 9px; margin: 0 3px;">{{ $label }}</span>
                                @endforeach
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Data Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Period</th>
                    @foreach($chartData['labels'] as $label)
                        <th>{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Events</strong></td>
                    @foreach($chartData['values'] as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    <div class="interpretation-section">
        <h2>Chart Analysis & Interpretation</h2>
        
        <div class="interpretation-item">
            <strong>Summary</strong>
            <p>{{ $interpretation['summary'] }}</p>
        </div>
        
        @if(isset($interpretation['peak']))
        <div class="interpretation-item">
            <strong>Peak Activity</strong>
            <p>{{ $interpretation['peak'] }}</p>
        </div>
        @endif
        
        @if(isset($interpretation['average']))
        <div class="interpretation-item">
            <strong>Average Activity</strong>
            <p>{{ $interpretation['average'] }}</p>
        </div>
        @endif
        
        <div class="interpretation-item">
            <strong>Trend Analysis</strong>
            <p>{{ $interpretation['trend'] }}</p>
        </div>
    </div>

    <div class="recommendations">
        <h3>Recommendations</h3>
        <ul>
            @foreach($interpretation['recommendations'] as $recommendation)
                <li>{{ $recommendation }}</li>
            @endforeach
        </ul>
    </div>

    <div class="footer">
        <p>SPUP-CDC Relief Distribution Management System</p>
        <p>Chart Analysis Report - {{ $title }}</p>
    </div>
</body>
</html>
