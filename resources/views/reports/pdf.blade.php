<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Block Register — Block List Report</title>
    <style>
        @font-face {
            font-family: 'SolaimanLipi';
            font-style: normal;
            font-weight: normal;
            src: url({{ public_path('fonts/SolaimanLipi.ttf') }}) format('truetype');
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'SolaimanLipi', 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 12px 14px;
            font-size: 10px;
            line-height: 1.35;
            color: #000;
            background: #fff;
            text-align: center;
        }

        .doc {
            max-width: 100%;
            margin: 0 auto;
        }

        .header {
            border: 1px solid #000;
            padding: 6px 8px 7px;
            margin-bottom: 10px;
            text-align: center;
        }

        .header-top {
            font-size: 11px;
            font-weight: bold;
            margin: 0 0 2px 0;
            letter-spacing: 0.03em;
        }

        .header-sub {
            font-size: 9px;
            font-weight: bold;
            margin: 0 0 1px 0;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .header-title {
            font-size: 9px;
            margin: 0 0 3px 0;
            color: #000;
        }

        .header-summary {
            font-size: 8px;
            margin: 0;
            line-height: 1.4;
            border-top: 1px solid #000;
            padding-top: 4px;
        }

        .header-summary strong {
            font-weight: bold;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9px;
            border: 1px solid #000;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: center;
            vertical-align: middle;
        }

        table.data th {
            background: #fff;
            color: #000;
            font-weight: bold;
            font-size: 11px;
            padding: 6px 4px;
        }

        table.data td.text-wrap {
            text-align: center;
        }

        .footer {
            margin-top: 12px;
            padding-top: 6px;
            border-top: 1px solid #000;
            font-size: 7px;
            color: #000;
            text-align: center;
            line-height: 1.4;
        }

        .footer p {
            margin: 2px 0;
        }

        tr.total-row td {
            font-weight: bold;
            border-top: 2px solid #000;
        }
    </style>
</head>
<body>
<div class="doc">
    <div class="header">
        <p class="header-top">MOUSUMI NGO</p>
        <p class="header-sub">Block Register</p>
        <p class="header-title">Block List Report</p>
        @if(isset($branch))
            <p class="header-summary">
                <strong>Branch:</strong> {{ $branch->branch_name }}@if($branch->branch_code) ({{ $branch->branch_code }})@endif
                &nbsp;·&nbsp; <strong>Total blocked:</strong> {{ $customers->count() }}
                &nbsp;·&nbsp; <strong>Date:</strong> {{ now()->format('d/m/Y') }}
            </p>
        @else
            <p class="header-summary">
                <strong>Date:</strong> {{ $reportPeriodLabel ?? 'All time' }}
                &nbsp;·&nbsp; <strong>Branches:</strong> {{ $branches->count() }}
                &nbsp;·&nbsp; <strong>Total blocked:</strong> {{ $totalCustomers }}
            </p>
        @endif
    </div>

    @if(isset($branch))
        <table class="data">
            <thead>
                <tr>
                    <th width="6%">SL</th>
                    <th width="24%">Customer details</th>
                    <th width="14%">Contact</th>
                    <th width="24%">Location</th>
                    <th width="14%">Block date</th>
                    <th width="10%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $index => $customer)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-wrap">
                        <strong>{{ $customer->name }}</strong>
                        @if($customer->name_bn)
                            <br><span style="font-size:8px;">{{ $customer->name_bn }}</span>
                        @endif
                        <br><span style="font-size:8px;">NID: {{ $customer->nid_number }}</span>
                    </td>
                    <td>{{ $customer->phone_number }}</td>
                    <td class="text-wrap">{{ Str::limit($customer->address, 48) }}</td>
                    <td>{{ $customer->created_at->format('d/m/Y') }}</td>
                    <td>Blocked</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th width="8%">SL</th>
                    <th width="32%">Branch name</th>
                    <th width="18%">Before block</th>
                    <th width="20%">Current month</th>
                    <th width="18%">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($branches as $index => $branch)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $branch->branch_name }}@if($branch->branch_code) ({{ $branch->branch_code }})@endif</strong>
                    </td>
                    <td>
                        @if(!empty($hasReportPeriod))
                            {{ $branch->before_current_month_count }}
                        @else
                            0
                        @endif
                    </td>
                    <td>
                        @if(!empty($hasReportPeriod))
                            {{ $branch->current_month_count }}
                        @else
                            {{ $branch->customers_count }}
                        @endif
                    </td>
                    <td>
                        @if(!empty($hasReportPeriod))
                            {{ $branch->total_through_period_count }}
                        @else
                            {{ $branch->customers_count }}
                        @endif
                    </td>
                </tr>
                @endforeach
                @php
                    $sumBefore = $branches->sum(fn ($b) => !empty($hasReportPeriod) ? (int) $b->before_current_month_count : 0);
                    $sumCurrent = $branches->sum(fn ($b) => !empty($hasReportPeriod) ? (int) $b->current_month_count : (int) $b->customers_count);
                    $sumTotalCol = $branches->sum(fn ($b) => !empty($hasReportPeriod) ? (int) $b->total_through_period_count : (int) $b->customers_count);
                @endphp
                <tr class="total-row">
                    <td></td>
                    <td>Total</td>
                    <td>{{ $sumBefore }}</td>
                    <td>{{ $sumCurrent }}</td>
                    <td>{{ $sumTotalCol }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Generated {{ now()->format('d/m/Y H:i') }} · Block Register · MOUSUMI NGO</p>
        <p>© {{ date('Y') }} MOUSUMI NGO. All rights reserved.</p>
    </div>
</div>
</body>
</html>
