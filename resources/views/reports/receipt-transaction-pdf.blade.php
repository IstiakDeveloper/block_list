<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Receipt Transaction Report</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
            color: #1a1a1a;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .filter-info {
            font-size: 10px;
            color: #333;
            margin: 3px 0;
        }

        .summary-box {
            background-color: #f8f9fa;
            border: 2px solid #000;
            padding: 10px;
            margin-bottom: 15px;
            display: table;
            width: 100%;
        }

        .summary-row {
            display: table-row;
        }

        .summary-cell {
            display: table-cell;
            padding: 5px 10px;
            border-right: 1px solid #ddd;
        }

        .summary-cell:last-child {
            border-right: none;
        }

        .summary-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            font-weight: normal;
        }

        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            margin-top: 2px;
        }

        .date-section {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .month-header {
            background-color: #343a40;
            color: white;
            border: 2px solid #000;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 10px;
            text-align: center;
            text-transform: uppercase;
        }

        .date-header {
            background-color: #6c757d;
            color: white;
            border: 1.5px solid #000;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 4px;
            margin-top: 6px;
        }

        .transaction-type {
            margin-bottom: 6px;
        }

        .type-header {
            background-color: #f1f3f5;
            padding: 3px 6px;
            font-weight: bold;
            font-size: 9px;
            border-left: 3px solid #000;
            margin-bottom: 3px;
        }

        .type-header.received {
            border-left-color: #28a745;
            color: #28a745;
        }

        .type-header.distributed {
            border-left-color: #007bff;
            color: #007bff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            margin-bottom: 8px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #333;
            padding: 8px 0;
            border-top: 1px solid #000;
            background-color: #fff;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">Receipt Transaction Report</div>
        <div class="title">Date-wise Receipt Transactions</div>
        <div class="filter-info">
            <strong>Period:</strong>
            @if($filters['start_date'] && $filters['end_date'])
                {{ \Carbon\Carbon::parse($filters['start_date'])->format('d-m-Y') }} to
                {{ \Carbon\Carbon::parse($filters['end_date'])->format('d-m-Y') }}
            @else
                All Dates
            @endif
            &nbsp;|&nbsp;
            <strong>Branch:</strong> {{ $filters['branch_name'] }}
        </div>
    </div>

    <!-- Summary Section -->
    <div class="summary-box">
        <div class="summary-row">
            <div class="summary-cell">
                <div class="summary-label">Total Received</div>
                <div class="summary-value">{{ number_format($summary['total_received']) }}</div>
            </div>
            <div class="summary-cell">
                <div class="summary-label">Total Distributed</div>
                <div class="summary-value">{{ number_format($summary['total_distributed']) }}</div>
            </div>
            <div class="summary-cell">
                <div class="summary-label">Available</div>
                <div class="summary-value">{{ number_format($summary['total_available']) }}</div>
            </div>
            <div class="summary-cell">
                <div class="summary-label">Total Transactions</div>
                <div class="summary-value">{{ number_format($summary['total_transactions']) }}</div>
            </div>
        </div>
    </div>

    <!-- Transaction Details -->
    @if(count($reportData) > 0)
        @foreach($reportData as $monthKey => $monthData)
            <!-- Month Header -->
            <div class="month-header">
                📅 {{ $monthData['month_name'] }}
            </div>

            @foreach($monthData['dates'] as $dateKey => $dayData)
                <div class="date-section">
                    <div class="date-header">
                        {{ $dayData['date'] }} ({{ $dayData['day_name'] }})
                    </div>

                    <!-- Received Transactions -->
                    @if(count($dayData['received']) > 0)
                        <div class="transaction-type">
                            <div class="type-header received">
                                📥 RECEIVED ({{ count($dayData['received']) }})
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 4%;">SL</th>
                                        <th style="width: 18%;" class="text-left">Branch</th>
                                        <th style="width: 8%;">Qty</th>
                                        <th style="width: 11%;">From</th>
                                        <th style="width: 11%;">To</th>
                                        <th style="width: 20%;" class="text-left">Received By</th>
                                        <th style="width: 8%;">Available</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $receivedSerial = 1;
                                        $receivedQtyTotal = 0;
                                        $receivedAvailableTotal = 0;
                                    @endphp
                                    @foreach($dayData['received'] as $transaction)
                                        @php
                                            $receivedQtyTotal += $transaction['quantity'];
                                            $receivedAvailableTotal += $transaction['available'];
                                        @endphp
                                        <tr>
                                            <td class="font-bold">{{ $receivedSerial++ }}</td>
                                            <td class="text-left">{{ $transaction['branch_name'] }}</td>
                                            <td class="font-bold" style="color: #28a745;">{{ number_format($transaction['quantity']) }}</td>
                                            <td>{{ number_format($transaction['from_number']) }}</td>
                                            <td>{{ number_format($transaction['to_number']) }}</td>
                                            <td class="text-left">{{ $transaction['received_by'] }}</td>
                                            <td class="font-bold" style="color: #6f42c1;">{{ number_format($transaction['available']) }}</td>
                                        </tr>
                                    @endforeach
                                    <!-- Total Row -->
                                    <tr style="background-color: #d4edda; font-weight: bold;">
                                        <td colspan="2" class="text-right font-bold">TOTAL:</td>
                                        <td class="font-bold">{{ number_format($receivedQtyTotal) }}</td>
                                        <td colspan="3"></td>
                                        <td class="font-bold">{{ number_format($receivedAvailableTotal) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <!-- Distributed Transactions -->
                    @if(count($dayData['distributed']) > 0)
                        <div class="transaction-type">
                            <div class="type-header distributed">
                                📤 DISTRIBUTED ({{ count($dayData['distributed']) }})
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 4%;">SL</th>
                                        <th style="width: 16%;" class="text-left">Branch</th>
                                        <th style="width: 7%;">Qty</th>
                                        <th style="width: 10%;">From</th>
                                        <th style="width: 10%;">To</th>
                                        <th style="width: 18%;" class="text-left">Given To</th>
                                        <th style="width: 10%;">PIN</th>
                                        <th style="width: 10%;">Book</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $distributedSerial = 1;
                                        $distributedQtyTotal = 0;
                                    @endphp
                                    @foreach($dayData['distributed'] as $transaction)
                                        @php
                                            $distributedQtyTotal += $transaction['quantity'];
                                        @endphp
                                        <tr>
                                            <td class="font-bold">{{ $distributedSerial++ }}</td>
                                            <td class="text-left">{{ $transaction['branch_name'] }}</td>
                                            <td class="font-bold" style="color: #007bff;">{{ number_format($transaction['quantity']) }}</td>
                                            <td>{{ number_format($transaction['from_number']) }}</td>
                                            <td>{{ number_format($transaction['to_number']) }}</td>
                                            <td class="text-left">{{ $transaction['given_to'] }}</td>
                                            <td>{{ $transaction['pin_number'] ?? '-' }}</td>
                                            <td>{{ $transaction['receipt_book_number'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                    <!-- Total Row -->
                                    <tr style="background-color: #cfe2ff; font-weight: bold;">
                                        <td colspan="2" class="text-right font-bold">TOTAL:</td>
                                        <td class="font-bold">{{ number_format($distributedQtyTotal) }}</td>
                                        <td colspan="5"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endforeach
        @endforeach
    @else
        <div class="no-data">
            No transactions found for the selected period.
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Generated: {{ $generatedAt }} | Report created by Mousumi NGO System
    </div>
</body>
</html>
