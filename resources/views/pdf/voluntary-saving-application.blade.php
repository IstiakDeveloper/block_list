<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Voluntary Saving Application</title>
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
        .application-details {
            margin-bottom: 20px;
        }
        .application-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .application-details table td {
            padding: 5px;
        }
        .deposits-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .deposits-table th, .deposits-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .deposits-table th {
            background-color: #f2f2f2;
        }
        .signature-box {
            margin-top: 50px;
            text-align: right;
        }
        .application-id {
            font-size: 12px;
            color: #666;
            margin-top: 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #FFF9C4;
            color: #F57F17;
        }
        .status-approved {
            background-color: #C8E6C9;
            color: #2E7D32;
        }
        .status-rejected {
            background-color: #FFCDD2;
            color: #C62828;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Voluntary Saving Application</h1>
        <div>
            <span class="status-badge status-{{ $voluntarySaving->status }}">
                {{ ucfirst($voluntarySaving->status) }}
            </span>
        </div>
    </div>

    <div class="application-details">
        <table>
            <tr>
                <td><strong>Application Date:</strong></td>
                <td>{{ date('d/m/Y', strtotime($voluntarySaving->application_date)) }}</td>
                <td><strong>Branch:</strong></td>
                <td>{{ $voluntarySaving->branch->branch_name }} ({{ $voluntarySaving->branch->branch_code }})</td>
            </tr>
            <tr>
                <td><strong>Somiti Name:</strong></td>
                <td>{{ $voluntarySaving->somiti_name }}</td>
                <td><strong>Somiti Code:</strong></td>
                <td>{{ $voluntarySaving->somiti_code }}</td>
            </tr>
            <tr>
                <td><strong>Member Name:</strong></td>
                <td>{{ $voluntarySaving->member_name }}</td>
                <td><strong>Member Code:</strong></td>
                <td>{{ $voluntarySaving->member_code }}</td>
            </tr>
            <tr>
                <td><strong>Profit:</strong></td>
                <td>{{ $voluntarySaving->profit ?? 'N/A' }}</td>
                <td><strong>Member Mobile:</strong></td>
                <td>{{ $voluntarySaving->member_mobile ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Applicant Name:</strong></td>
                <td>{{ $voluntarySaving->applicant_name }}</td>
                <td><strong>Designation:</strong></td>
                <td>{{ $voluntarySaving->designation ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <h3>Deposit Details</h3>
    <table class="deposits-table">
        <thead>
            <tr>
                <th>Deposit Date</th>
                <th>Deposit Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; @endphp
            @foreach($voluntarySaving->deposits as $deposit)
                @php $totalAmount += $deposit->deposit_amount; @endphp
                <tr>
                    <td>{{ date('d/m/Y', strtotime($deposit->deposit_date)) }}</td>
                    <td>{{ number_format($deposit->deposit_amount, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <th>Total</th>
                <th>{{ number_format($totalAmount, 2) }}</th>
            </tr>
        </tbody>
    </table>

    <div class="signature-box">
        @if($voluntarySaving->signature)
            <img src="{{ public_path('storage/'.$voluntarySaving->signature) }}" alt="Signature" height="60">
        @endif
        <p>{{ $voluntarySaving->applicant_name }}</p>
        <p>{{ $voluntarySaving->designation }}</p>
    </div>

    <div class="application-id">
        Application ID: {{ $voluntarySaving->id }}<br>
        Generated on: {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>
