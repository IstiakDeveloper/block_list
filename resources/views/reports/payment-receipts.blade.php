<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipts Payment Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            line-height: 1.3;
            margin: 0;
            padding: 20px;
        }

        .header {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
        }

        .company-info {
            text-align: left;
            float: left;
            width: 40%;
        }

        .report-info {
            text-align: right;
            float: right;
            width: 40%;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .meta-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .meta-item {
            display: table-cell;
            padding: 8px;
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            font-size: 8px;
            text-align: center;
        }

        .meta-label {
            font-weight: bold;
            color: #4b5563;
            margin-bottom: 2px;
        }

        .meta-value {
            color: #1f2937;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8px;
        }

        th, td {
            padding: 5px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        th {
            background: #f3f4f6;
            font-weight: bold;
            color: #4b5563;
        }

        .branch-summary {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .branch-header {
            font-size: 11px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
            padding: 5px;
            background: #f0f9ff;
        }

        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .stat-cell {
            display: table-cell;
            padding: 5px;
            border: 1px solid #e5e7eb;
            text-align: center;
            width: 25%;
        }

        .stat-label {
            font-size: 7px;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .stat-value {
            font-size: 9px;
            font-weight: bold;
            color: #1f2937;
        }

        .transactions-table th {
            white-space: nowrap;
            font-size: 7px;
        }

        .status-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 3px;
        }

        .status-good { background: #059669; }
        .status-warning { background: #d97706; }
        .status-danger { background: #dc2626; }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 5px;
            text-align: center;
            font-size: 7px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #4b5563;
            margin: 15px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .highlight {
            background: #f0f9ff;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header clearfix">
        <div class="company-info">
            <div class="company-name">Mousumi NGO</div>
            <div>Payment Receipts Report</div>
        </div>
        <div class="report-info">
            <div><strong>Period:</strong> {{ $meta['start_date'] }} to {{ $meta['end_date'] }}</div>
            <div><strong>Branch:</strong> {{ $meta['branch'] }}</div>
            <div><strong>Generated:</strong> {{ $meta['generated_at'] }}</div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="meta-grid">
        <div class="meta-item">
            <div class="meta-label">TOTAL RECEIVED</div>
            <div class="meta-value">{{ number_format($totals['total_period_received']) }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">TOTAL DISTRIBUTED</div>
            <div class="meta-value">{{ number_format($totals['total_period_distributed']) }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">CURRENTLY AVAILABLE</div>
            <div class="meta-value">{{ number_format($totals['total_available']) }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">ACTIVE BRANCHES</div>
            <div class="meta-value">{{ $totals['total_branches'] }}</div>
        </div>
    </div>

    <!-- Branch Summaries -->
    <div class="section-title">Branch-wise Summary</div>
    @foreach($branches as $branch)
    <div class="branch-summary">
        <div class="branch-header">{{ $branch->branch_name }}</div>
        <div class="stats-grid">
            <div class="stat-cell">
                <div class="stat-label">PERIOD RECEIVED</div>
                <div class="stat-value">{{ number_format($branch->period_received) }}</div>
            </div>
            <div class="stat-cell">
                <div class="stat-label">PERIOD DISTRIBUTED</div>
                <div class="stat-value">{{ number_format($branch->period_distributed) }}</div>
            </div>
            <div class="stat-cell">
                <div class="stat-label">ALL TIME RECEIVED</div>
                <div class="stat-value">{{ number_format($branch->all_time_received) }}</div>
            </div>
            <div class="stat-cell">
                <div class="stat-label">AVAILABLE
                    <span class="status-indicator
                        {{ $branch->current_available >= 500 ? 'status-good' :
                           ($branch->current_available >= 100 ? 'status-warning' : 'status-danger') }}">
                    </span>
                </div>
                <div class="stat-value">{{ number_format($branch->current_available) }}</div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Transactions Table -->
    <div class="section-title">Recent Transactions</div>
    <table class="transactions-table">
        <thead>
            <tr>
                <th>DATE</th>
                <th>BRANCH</th>
                <th>RECEIVED</th>
                <th>DISTRIBUTED</th>
                <th>AVAILABLE</th>
                <th>BOOK #</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr class="{{ $loop->even ? 'highlight' : '' }}">
                <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}</td>
                <td>{{ $transaction->branch->branch_name }}</td>
                <td style="color: #059669">{{ $transaction->receive_quantity ?: '-' }}</td>
                <td style="color: #2563eb">{{ $transaction->given_quantity ?: '-' }}</td>
                <td>{{ $transaction->available_receipts }}</td>
                <td>{{ $transaction->receipt_book_number ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Page <span class="page-number"></span> | Generated by {{ config('app.name') }}
    </div>
</body>
</html>
