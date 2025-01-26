<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Branch Users Report</title>
    <style>
        body {
            font-family: bangla, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .date-range {
            font-size: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        th, td {
            border: 0.5px solid #ddd;
            padding: 4px;
            text-align: left;
            vertical-align: middle;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            font-size: 9px;
        }
        .branch-name {
            font-weight: bold;
            font-size: 10px;
        }
        .branch-code {
            color: #666;
            font-size: 8px;
        }
        .user-data {
            display: inline-block;
            margin-right: 15px;
            white-space: nowrap;
        }
        .user-entries {
            color: #333;
            margin-left: 3px;
        }
        .total-entries {
            font-weight: bold;
            color: #2563eb;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #666;
            padding: 5px 0;
            border-top: 0.5px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Branch Users Report</div>
        <div class="date-range">
            Period:
            @if($dateRange === 'all')
                All Time
            @elseif($dateRange === 'month')
                This Month
            @elseif($dateRange === 'week')
                Last 7 Days
            @else
                {{ $startDate }} to {{ $endDate }}
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Branch</th>
                <th>Users & Entries</th>
                <th style="width: 15%;">Total Entries</th>
            </tr>
        </thead>
        <tbody>
            @foreach($branches as $branch)
                <tr>
                    <td>
                        <span class="branch-name">{{ $branch['name'] }}</span>
                        <span class="branch-code">({{ $branch['code'] }})</span>
                    </td>
                    <td>
                        @foreach($branch['users'] as $user)
                            <span class="user-data">
                                {{ $user['name'] }}:
                                <span class="user-entries">{{ $user['entries'] }}</span>
                                ({{ round(($user['entries'] / $branch['total'] * 100), 1) }}%)
                            </span>
                        @endforeach
                    </td>
                    <td class="total-entries">{{ $branch['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated: {{ now()->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
