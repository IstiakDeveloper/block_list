<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Block Register — Payment receipts report</title>
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
            padding: 8px 10px;
            font-size: 8px;
            line-height: 1.3;
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
            padding: 5px 6px 6px;
            margin-bottom: 6px;
            text-align: center;
        }

        .header-top {
            font-size: 10px;
            font-weight: bold;
            margin: 0 0 1px 0;
            letter-spacing: 0.03em;
        }

        .header-sub {
            font-size: 8px;
            font-weight: bold;
            margin: 0 0 1px 0;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .header-title {
            font-size: 8px;
            margin: 0 0 2px 0;
        }

        .header-summary {
            font-size: 7px;
            margin: 0;
            line-height: 1.4;
            border-top: 1px solid #000;
            padding-top: 3px;
        }

        .header-summary strong {
            font-weight: bold;
        }

        .section-title {
            font-size: 8px;
            font-weight: bold;
            margin: 7px 0 4px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #000;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 7px;
            border: 1px solid #000;
            table-layout: fixed;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        table.data th {
            background: #fff;
            color: #000;
            font-weight: bold;
            font-size: 8px;
            padding: 4px 2px;
        }

        table.data td {
            font-size: 7px;
        }

        .num {
            font-variant-numeric: tabular-nums;
        }

        .footer {
            margin-top: 8px;
            padding-top: 5px;
            border-top: 1px solid #000;
            font-size: 6px;
            text-align: center;
            line-height: 1.35;
        }

        .footer p {
            margin: 2px 0;
        }
    </style>
</head>
<body>
<div class="doc">
    <div class="header">
        <p class="header-top">MOUSUMI NGO</p>
        <p class="header-sub">Block Register</p>
        <p class="header-title">Payment receipts report</p>
        <p class="header-summary">
            <strong>Date:</strong> {{ $meta['start_date'] }} – {{ $meta['end_date'] }}
            &nbsp;·&nbsp; <strong>Branch:</strong> {{ $meta['branch'] }}
            &nbsp;·&nbsp; <strong>Generated:</strong> {{ $meta['generated_at'] }}
        </p>
    </div>

    <p class="section-title">Branch-wise summary</p>
    <table class="data">
        <thead>
            <tr>
                <th width="28%">Branch</th>
                <th width="18%">Period received</th>
                <th width="18%">Period distributed</th>
                <th width="18%">All-time received</th>
                <th width="18%">Available</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($branches as $branch)
                <tr>
                    <td style="text-align: center;">
                        <strong>{{ $branch['branch_name'] }}</strong>@if(!empty($branch['branch_code'])) ({{ $branch['branch_code'] }})@endif
                    </td>
                    <td class="num">{{ number_format($branch['period_received']) }}</td>
                    <td class="num">{{ number_format($branch['period_distributed']) }}</td>
                    <td class="num">{{ number_format($branch['all_time_received']) }}</td>
                    <td class="num">{{ number_format($branch['current_available']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(!empty($includeTransactions))
        <p class="section-title">Transactions</p>
        <table class="data">
            <thead>
                <tr>
                    <th width="12%">Date</th>
                    <th width="22%">Branch</th>
                    <th width="14%">Received</th>
                    <th width="14%">Distributed</th>
                    <th width="14%">Available</th>
                    <th width="18%">Book #</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $transaction)
                    <tr>
                        <td>{{ date('d/m/Y', strtotime($transaction['transaction_date'])) }}</td>
                        <td>{{ $transaction['branch_name'] }}</td>
                        <td class="num">{{ $transaction['receive_quantity'] !== null ? number_format($transaction['receive_quantity']) : '—' }}</td>
                        <td class="num">{{ $transaction['given_quantity'] !== null ? number_format($transaction['given_quantity']) : '—' }}</td>
                        <td class="num">{{ number_format($transaction['available_receipts']) }}</td>
                        <td>{{ $transaction['receipt_book_number'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No transactions in this date range.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Block Register · MOUSUMI NGO · Payment receipts</p>
        <p>© {{ date('Y') }} MOUSUMI NGO. All rights reserved.</p>
    </div>
</div>
</body>
</html>
