<!DOCTYPE html>
<html>
<head>
    <title>Transaction Summary Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1a56db;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #4b5563;
            margin: 5px 0 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #4b5563;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
            color: #6b7280;
        }
        .total-row td {
            font-weight: bold;
            background-color: #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Transaction Summary Report</h1>
        <p>Generated on {{ $generated_at }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>From</th>
                <th>To</th>
                <th>Receipts</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $transaction)
            <tr>
                <td>{{ $transaction->created_at->format('d/m/Y') }}</td>
                <td>{{ $transaction->transaction_type }}</td>
                <td>{{ $transaction->fromBranch?->branch_name ?? 'System' }}</td>
                <td>{{ $transaction->toBranch?->branch_name ?? $transaction->given_to ?? '-' }}</td>
                <td>{{ number_format($transaction->total_receipts) }}</td>
                <td>{{ $transaction->status }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4">Total</td>
                <td>{{ number_format($data->sum('total_receipts')) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Payment Receipt Management System</p>
    </div>
</body>
</html>
