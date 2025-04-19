<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Voluntary Saving Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .filter-info {
            margin-bottom: 20px;
            font-size: 14px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        .report-table th {
            background-color: #f2f2f2;
        }
        .status-pending {
            color: #F57F17;
        }
        .status-approved {
            color: #2E7D32;
        }
        .status-rejected {
            color: #C62828;
        }
        .summary {
            margin-top: 30px;
        }
        .summary table {
            width: 50%;
            border-collapse: collapse;
        }
        .summary table th, .summary table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .footer {
            margin-top: 50px;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Voluntary Saving Applications Report</h1>
    </div>

    <div class="filter-info">
        <p><strong>Report Filters:</strong></p>
        <ul>
            @if(isset($filters['status']))
                <li>Status: {{ ucfirst($filters['status']) }}</li>
            @endif
            @if(isset($filters['branch_id']) && $filters['branch_id'])
                @php
                    $branch = App\Models\Branch::find($filters['branch_id']);
                    $branchName = $branch ? $branch->branch_name : 'Unknown';
                @endphp
                <li>Branch: {{ $branchName }}</li>
            @endif
            @if(isset($filters['from_date']) && $filters['from_date'])
                <li>From Date: {{ date('d/m/Y', strtotime($filters['from_date'])) }}</li>
            @endif
            @if(isset($filters['to_date']) && $filters['to_date'])
                <li>To Date: {{ date('d/m/Y', strtotime($filters['to_date'])) }}</li>
            @endif
        </ul>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Branch</th>
                <th>Somiti</th>
                <th>Member</th>
                <th>Total Amount</th>
                <th>Profit</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalApplications = count($voluntarySavings);
                $totalPending = 0;
                $totalApproved = 0;
                $totalRejected = 0;
                $totalAmount = 0;
                $totalProfit = 0;
            @endphp

            @foreach($voluntarySavings as $saving)
                @php
                    $savingTotalAmount = $saving->deposits->sum('deposit_amount');
                    $totalAmount += $savingTotalAmount;

                    if($saving->profit) {
                        $totalProfit += $saving->profit;
                    }

                    if($saving->status == 'pending') {
                        $totalPending++;
                    } elseif($saving->status == 'approved') {
                        $totalApproved++;
                    } elseif($saving->status == 'rejected') {
                        $totalRejected++;
                    }
                @endphp
                <tr>
                    <td>{{ $saving->id }}</td>
                    <td>{{ date('d/m/Y', strtotime($saving->application_date)) }}</td>
                    <td>{{ $saving->branch->branch_name }}</td>
                    <td>{{ $saving->somiti_name }} ({{ $saving->somiti_code }})</td>
                    <td>{{ $saving->member_name }} ({{ $saving->member_code }})</td>
                    <td>{{ number_format($savingTotalAmount, 2) }}</td>
                    <td>{{ $saving->profit ? number_format($saving->profit, 2) : 'N/A' }}</td>
                    <td class="status-{{ $saving->status }}">{{ ucfirst($saving->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>Summary</h3>
        <table>
            <tr>
                <th>Total Applications</th>
                <td>{{ $totalApplications }}</td>
            </tr>
            <tr>
                <th>Pending</th>
                <td>{{ $totalPending }}</td>
            </tr>
            <tr>
                <th>Approved</th>
                <td>{{ $totalApproved }}</td>
            </tr>
            <tr>
                <th>Rejected</th>
                <td>{{ $totalRejected }}</td>
            </tr>
            <tr>
                <th>Total Amount</th>
                <td>{{ number_format($totalAmount, 2) }}</td>
            </tr>
            <tr>
                <th>Total Profit</th>
                <td>{{ number_format($totalProfit, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Generated on: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
