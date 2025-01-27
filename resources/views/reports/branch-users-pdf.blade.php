<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Branch Users Report</title>
    <style>
        body {
            font-family: bangla, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
            color: #1a1a1a;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .date-range {
            font-size: 12px;
            color: #333;
            font-weight: 500;
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 40px;
        }

        th,
        td {
            border: 1.5px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .branch-name {
            font-weight: bold;
            font-size: 11px;
            color: #000;
            display: block;
        }

        .branch-code {
            color: #444;
            font-size: 9px;
            display: block;
            margin-top: 2px;
        }

        .user-name {
            font-weight: 500;
            display: block;
        }

        .user-entries {
            color: #000;
            display: block;
            margin-top: 2px;
            font-weight: bold;
        }

        .user-percentage {
            color: #555;
            font-size: 9px;
            display: block;
            margin-top: 2px;
        }

        .total-entries {
            font-weight: bold;
            color: #000;
        }

        .grand-total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .grand-total-row td {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #333;
            padding: 8px 0;
            border-top: 2px solid #000;
            background-color: #fff;
        }

        .branch-info {
            white-space: nowrap;
        }

        .branch-name {
            font-weight: bold;
            font-size: 11px;
            color: #000;
            display: inline;
        }

        .branch-code {
            color: #444;
            font-size: 11px;
            display: inline;
            margin-left: 5px;
        }

        .user-cell {
            white-space: nowrap;
        }

        .user-id {
            color: #666;
            margin-right: 3px;
        }

        .user-name {
            font-weight: 500;
            display: inline;
        }

        .user-entries {
            color: #000;
            display: inline;
            font-weight: bold;
            margin-left: 5px;
        }

        .user-percentage {
            color: #555;
            font-size: 9px;
            display: inline;
            margin-left: 3px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">Branch Users Report</div>
        <div class="date-range">
            Period:
            @if ($dateRange === 'all')
                All Time
            @elseif($dateRange === 'month')
                This Month
            @elseif($dateRange === 'week')
                Last 7 Days
            @else
                {{ Carbon\Carbon::parse($startDate)->format('d-m-Y') }} to
                {{ Carbon\Carbon::parse($endDate)->format('d-m-Y') }}
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th>Branch</th>
                    <th>BM</th>
                    <th>RM</th>
                    <th>ZM</th>
                    <th>DMF</th>
                    <th>Total Entries</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Initialize grand totals for each role and overall total
                    $grandTotals = [
                        'BM' => 0,
                        'RM' => 0,
                        'ZM' => 0,
                        'DMF' => 0,
                        'total' => 0,
                    ];
                @endphp

                @foreach ($branches as $branch)
                    <tr>
                        <td>
                            <div class="branch-info">
                                <span class="branch-name">{{ $branch['name'] }}</span>
                                <span class="branch-code">({{ $branch['code'] }})</span>
                            </div>
                        </td>
                        @php
                            // Define the order of user roles
                            $roles = ['BM', 'RM', 'ZM', 'DMF'];
                            $usersByRole = [];

                            // Group users by their roles
                            foreach ($branch['users'] as $user) {
                                $usersByRole[$user['role']] = $user;
                            }
                        @endphp
                        @foreach ($roles as $role)
                            @if (isset($usersByRole[$role]))
                                @php
                                    $user = $usersByRole[$role];
                                    // Add to the grand total for this role
                                    $grandTotals[$role] += $user['entries'];
                                @endphp
                                <td class="user-cell">
                                    <span class="user-name">{{ $user['name'] }}</span>
                                    <span class="user-entries">{{ number_format($user['entries']) }}</span>
                                    <span class="user-percentage">
                                        ({{ round(($user['entries'] / $branch['total']) * 100, 1) }}%)
                                    </span>
                                </td>
                            @else
                                <td>-</td>
                            @endif
                        @endforeach
                        <td class="total-entries">{{ number_format($branch['total']) }}</td>
                    </tr>
                    @php
                        // Add to the overall grand total
                        $grandTotals['total'] += $branch['total'];
                    @endphp
                @endforeach

                <!-- Grand Total Row -->
                <tr class="grand-total-row">
                    <td style="text-align: right;">Grand Total</td>
                    @foreach ($roles as $role)
                        <td class="total-entries">{{ number_format($grandTotals[$role]) }}</td>
                    @endforeach
                    <td class="total-entries">{{ number_format($grandTotals['total']) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            Generated: {{ now()->format('d-m-Y H:i:s') }}
        </div>
</body>

</html>
