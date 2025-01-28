<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Receipts Payment Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding: 15px;
            border-radius: 8px;
            color: black;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .report-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .branch-info {
            font-size: 15px;
            margin-bottom: 5px;
        }

        .period-info {
            font-size: 13px;
        }

        .summary-section {
            margin: 20px 0;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .summary-container {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .summary-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 12px;
            color: #1e40af;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 5px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .summary-box {
            padding: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }

        .summary-label {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
        }

        .table-section {
            margin-top: 25px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        th, td {
            padding: 6px;
            border: 1px solid #e2e8f0;
            white-space: nowrap;
            text-align: center;
        }

        th {
            background-color: #f1f5f9;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .receive-section {
            background-color: #f0fdf4;
            color: #166534;
        }

        .distribute-section {
            background-color: #eff6ff;
            color: #1e40af;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #64748b;
        }

        .page-number {
            text-align: right;
            font-size: 9px;
            color: #64748b;
            margin-top: 5px;
        }

        @page {
            margin: 20px;
        }

        .summary-section {
            margin: 20px 0;
            padding: 15px;
            background: #ffffff;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
            background: #ffffff;
        }

        .summary-header {
            background: #f8fafc;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
            text-align: center;
            border-bottom: 2px solid #e2e8f0;
        }

        .column-headers th {
            background: #f1f5f9;
            padding: 8px;
            font-size: 12px;
            font-weight: bold;
            color: #475569;
            text-align: center;
            border: 1px solid #e2e8f0;
        }

        .label {
            padding: 10px;
            font-size: 12px;
            color: #64748b;
            background: #f8fafc;
            font-weight: bold;
            border: 1px solid #e2e8f0;
        }

        .value {
            padding: 10px;
            font-size: 14px;
            color: #1e293b;
            text-align: center;
            font-weight: bold;
            border: 1px solid #e2e8f0;
        }

        /* Hover effects */
        .summary-table tr:hover {
            background-color: #f8fafc;
        }

        /* Responsive adjustments */
        @media print {
            .summary-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-name">Mousumi NGO</div>
        <div class="report-title">Receipts Payment Report</div>
        <div class="branch-info">Branch: {{ $branchName }}</div>
        <div class="period-info">Period: {{ date('d/m/Y', strtotime($startDate)) }} -
            {{ date('d/m/Y', strtotime($endDate)) }}</div>
    </div>

    <div class="summary-section">
        <table class="summary-table">
            <tr>
                <td colspan="4" class="summary-header">Summary Statistics</td>
            </tr>
            <tr class="column-headers">
                <th></th>
                <th>Period Summary</th>
                <th>All Time Summary</th>
                <th>Current Status</th>
            </tr>
            <tr>
                <td class="label">Received</td>
                <td class="value">{{ $periodSummary['received'] ?? 0 }}</td>
                <td class="value">{{ $allTimeSummary['received'] ?? 0 }}</td>
                <td class="value" rowspan="2">{{ $allTimeSummary['available'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Disbursement</td>
                <td class="value">{{ $periodSummary['distributed'] ?? 0 }}</td>
                <td class="value">{{ $allTimeSummary['distributed'] ?? 0 }}</td>
            </tr>
        </table>
    </div>

    <div class="table-section">
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th colspan="5" class="receive-section">Receive Section</th>
                    <th colspan="6" class="distribute-section">Disbursement Section</th>
                </tr>
                <tr>
                    <th>Date</th>
                    <th>Qty</th>
                    <th>From #</th>
                    <th>To #</th>
                    <th>Total</th>
                    <th>Received By</th>
                    <th>Disburse To</th>
                    <th>PIN</th>
                    <th>From #</th>
                    <th>To #</th>
                    <th>Book #</th>
                    <th>Available</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipts as $receipt)
                    <tr>
                        <td>{{ date('d/m/Y', strtotime($receipt->transaction_date)) }}</td>
                        <td>{{ $receipt->receive_quantity ?: '-' }}</td>
                        <td>{{ $receipt->receipt_from_number ?: '-' }}</td>
                        <td>{{ $receipt->receipt_to_number ?: '-' }}</td>
                        <td>{{ $receipt->total_cumulative_quantity }}</td>
                        <td>{{ $receipt->received_by ?: '-' }}</td>
                        <td>{{ $receipt->given_to ?: '-' }}</td>
                        <td>{{ $receipt->pin_number ?: '-' }}</td>
                        <td>{{ $receipt->given_from_number ?: '-' }}</td>
                        <td>{{ $receipt->given_to_number ?: '-' }}</td>
                        <td>{{ $receipt->receipt_book_number ?: '-' }}</td>
                        <td>{{ $receipt->available_receipts }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" style="text-align: center;">No records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Generated on {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>

</html>
