<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Block List Report - Mousumi NGO</title>
    <style>
         @font-face {
            font-family: 'SolaimanLipi';
            font-style: normal;
            font-weight: normal;
            src: url({{ public_path('fonts/SolaimanLipi.ttf') }}) format('truetype');
        }

        body {
            font-family: 'SolaimanLipi', sans-serif;
            margin: 0;
            padding: 15px;
            font-size: 12px;
            line-height: 1.4;
        }
        .bangla-text {
            font-family: 'SolaimanLipi', sans-serif;
        }
        .header {
            border-bottom: 2px solid #1f4e79;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .logo-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .report-title {
            color: #1f4e79;
            font-size: 20px;
            margin: 0;
            text-align: center;
        }
        .subtitle {
            color: #666;
            font-size: 11px;
            margin: 5px 0;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        th, td {
            border: 0.5px solid #ccc;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #1f4e79;
            color: white;
            font-weight: normal;
        }
        .summary-box {
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 3px solid #1f4e79;
        }
        .summary-box h3 {
            color: #1f4e79;
            margin: 0 0 5px 0;
            font-size: 13px;
        }
        .summary-item {
            display: inline-block;
            margin-right: 20px;
            color: #333;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 9px;
            text-align: center;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .confidential {
            color: #cc0000;
            font-size: 8px;
            text-transform: uppercase;
            text-align: center;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-title">
            <h1 class="report-title">MOUSUMI NGO</h1>
        </div>
        <p class="subtitle">Block List Report</p>
        <p class="confidential">Confidential Document</p>
    </div>

    @if(isset($branch))
        <!-- Single Branch Block List Report -->
        <div class="summary-box">
            <h3>Branch Information</h3>
            <div class="summary-item">Branch: {{ $branch->branch_name }} ({{ $branch->branch_code }})</div>
            <div class="summary-item">Total Blocked: {{ $customers->count() }}</div>
            <div class="summary-item">Report Date: {{ now()->format('d/m/Y') }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="5%">SL</th>
                    <th width="25%">Customer Details</th>
                    <th width="20%">Contact</th>
                    <th width="25%">Location</th>
                    <th width="15%">Block Date</th>
                    <th width="10%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $index => $customer)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $customer->name }}</strong>
                        @if($customer->name_bn)
                        <br><small>{{ $customer->name_bn }}</small>
                        @endif
                        <br><small>NID: {{ $customer->nid_number }}</small>
                    </td>
                    <td>
                        {{ $customer->phone_number }}
                    </td>
                    <td>{{ Str::limit($customer->address, 40) }}</td>
                    <td>{{ $customer->created_at->format('d/m/Y') }}</td>
                    <td>Blocked</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <!-- Overall Block List Summary -->
        <div class="summary-box">
            <h3>Block List Summary</h3>
            <div class="summary-item">Total Branches: {{ $branches->count() }}</div>
            <div class="summary-item">Total Blocked: {{ $totalCustomers }}</div>
            <div class="summary-item">Report Period: Last 30 Days</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="5%">SL</th>
                    <th width="20%">Branch</th>
                    <th width="15%">Total Blocked</th>
                    <th width="15%">Last 30 Days</th>
                    <th width="15%">Last 7 Days</th>
                    <th width="30%">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($branches as $index => $branch)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $branch->branch_name }}</strong>
                        <small>{{ $branch->branch_code }}</small>
                    </td>
                    <td>{{ $branch->customers_count }}</td>
                    <td>{{ $branch->customers->where('created_at', '>=', now()->subDays(30))->count() }}</td>
                    <td>{{ $branch->customers->where('created_at', '>=', now()->subDays(7))->count() }}</td>
                    <td>{{ $branch->customers_count > 0 ? 'Active' : 'No Records' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if(isset($monthlyTrend) && $monthlyTrend->count() > 0)
        <div class="summary-box">
            <h3>Monthly Blocking Trend (Last 6 Months)</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>New Blocks</th>
                    <th>Trend</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyTrend->take(6) as $trend)
                <tr>
                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $trend->month)->format('M Y') }}</td>
                    <td>{{ $trend->count }}</td>
                    <td>{{ $trend->count > ($monthlyTrend->skip(1)->first()->count ?? 0) ? '↑' : '↓' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    @endif

    <div class="footer">
        <p>This is a confidential document of Mousumi NGO. Generated on {{ now()->format('d/m/Y H:i') }}</p>
        <p>© {{ date('Y') }} Mousumi NGO. All rights reserved.</p>
    </div>
</body>
</html>
