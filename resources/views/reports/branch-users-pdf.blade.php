<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Block Register — Branch Users Report</title>
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
            font-size: 8px;
            border: 1px solid #000;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
            vertical-align: top;
        }

        table.data th {
            background: #fff;
            color: #000;
            font-weight: bold;
            font-size: 9px;
            padding: 5px 3px;
            vertical-align: middle;
        }

        .branch-line {
            font-weight: bold;
            vertical-align: middle;
        }

        .user-cell {
            font-size: 8px;
            line-height: 1.35;
        }

        .user-line {
            margin: 2px 0;
            font-size: 7px;
        }

        .user-name {
            font-weight: 600;
        }

        .user-entries {
            font-weight: bold;
        }

        .user-pct {
            font-size: 7px;
            color: #000;
        }

        .role-tag {
            font-size: 6px;
            color: #333;
        }

        .grand-total-row td {
            font-weight: bold;
            border-top: 2px solid #000;
            vertical-align: middle;
        }

        .footer {
            margin-top: 12px;
            padding-top: 6px;
            border-top: 1px solid #000;
            font-size: 7px;
            text-align: center;
            line-height: 1.4;
        }

        .footer p {
            margin: 2px 0;
        }
    </style>
</head>
<body>
@php
    $roleKeys = ['BM', 'RM', 'ZM', 'DMF'];
    $grandTotals = ['BM' => 0, 'RM' => 0, 'ZM' => 0, 'DMF' => 0, 'total' => 0];
@endphp

<div class="doc">
    <div class="header">
        <p class="header-top">MOUSUMI NGO</p>
        <p class="header-sub">Block Register</p>
        <p class="header-title">Branch users report</p>
        <p class="header-summary">
            <strong>Date:</strong> {{ $reportPeriodLabel ?? 'All time' }}
            &nbsp;·&nbsp; <strong>Branches:</strong> {{ $branches->count() }}
            &nbsp;·&nbsp; <strong>Total entries:</strong> {{ $branches->sum('total') }}
        </p>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="18%">Branch</th>
                <th width="20%">BM</th>
                <th width="16%">RM</th>
                <th width="16%">ZM</th>
                <th width="16%">DMF</th>
                <th width="12%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($branches as $branch)
                @php
                    $branchTotal = (int) $branch['total'];
                    $buckets = $branch['role_buckets'] ?? [];
                @endphp
                <tr>
                    <td class="branch-line">
                        {{ $branch['name'] }}@if(!empty($branch['code'])) ({{ $branch['code'] }})@endif
                    </td>
                    @foreach ($roleKeys as $role)
                        @php
                            $bucket = $buckets[$role] ?? ['total' => 0, 'lines' => []];
                            $bt = (int) ($bucket['total'] ?? 0);
                            if ($bt > 0) {
                                $grandTotals[$role] += $bt;
                            }
                            $pct = $branchTotal > 0 && $bt > 0 ? round(($bt / $branchTotal) * 100, 1) : null;
                        @endphp
                        <td class="user-cell">
                            @if ($bt === 0)
                                —
                            @else
                                @foreach ($bucket['lines'] ?? [] as $line)
                                    <div class="user-line">
                                        <span class="user-name">{{ $line['name'] }}</span>
                                        @if ($role === 'BM' && !empty($line['role_label']))
                                            <span class="role-tag"> ({{ $line['role_label'] }})</span>
                                        @endif
                                        <br>
                                        <span class="user-entries">{{ number_format($line['entries']) }}</span>
                                    </div>
                                @endforeach
                                @if (count($bucket['lines'] ?? []) > 1)
                                    <div class="user-line" style="margin-top:4px;border-top:1px solid #ccc;padding-top:3px;">
                                        <span class="user-entries">{{ number_format($bt) }}</span>
                                        @if ($pct !== null)
                                            <span class="user-pct"> ({{ $pct }}%)</span>
                                        @endif
                                    </div>
                                @elseif ($pct !== null)
                                    <span class="user-pct">({{ $pct }}%)</span>
                                @endif
                            @endif
                        </td>
                    @endforeach
                    <td class="user-entries">{{ number_format($branchTotal) }}</td>
                </tr>
                @php
                    $grandTotals['total'] += $branchTotal;
                @endphp
            @endforeach

            <tr class="grand-total-row">
                <td>Grand total</td>
                @foreach ($roleKeys as $role)
                    @php
                        $gt = (int) $grandTotals[$role];
                        $grandPct = $grandTotals['total'] > 0 ? round(($gt / $grandTotals['total']) * 100, 1) : null;
                    @endphp
                    <td>
                        {{ number_format($gt) }}
                        @if ($grandPct !== null && $gt > 0)
                            <span class="user-pct">({{ $grandPct }}%)</span>
                        @endif
                    </td>
                @endforeach
                <td class="user-entries">{{ number_format($grandTotals['total']) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Generated {{ now()->format('d/m/Y H:i') }} · Block Register · MOUSUMI NGO</p>
        <p>© {{ date('Y') }} MOUSUMI NGO. All rights reserved.</p>
    </div>
</div>
</body>
</html>
