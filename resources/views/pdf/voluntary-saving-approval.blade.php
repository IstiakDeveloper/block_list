<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Voluntary Saving Approval Letter</title>
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
        .letter-body {
            margin-bottom: 30px;
        }
        .approval-details {
            margin-bottom: 20px;
        }
        .approval-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .approval-details table td {
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
        .approval-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            background-color: #C8E6C9;
            color: #2E7D32;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Voluntary Saving Approval Letter</h1>
        <div>
            <span class="approval-badge">APPROVED</span>
        </div>
    </div>

    <div class="letter-body">
        <p>Date: {{ date('d/m/Y') }}</p>
        <p>
            To,<br>
            {{ $voluntarySaving->member_name }}<br>
            Member Code: {{ $voluntarySaving->member_code }}<br>
            Somiti: {{ $voluntarySaving->somiti_name }} ({{ $voluntarySaving->somiti_code }})
        </p>

        <p>Subject: Approval of Voluntary Saving Application</p>

        <p>Dear {{ $voluntarySaving->member_name }},</p>

        <p>We are pleased to inform you that your Voluntary Saving Application has been approved. The details of your application are as follows:</p>
    </div>

    <div class="approval-details">
        <table>
            <tr>
                <td><strong>Application Date:</strong></td>
                <td>{{ date('d/m/Y', strtotime($voluntarySaving->application_date)) }}</td>
                <td><strong>Branch:</strong></td>
                <td>{{ $voluntarySaving->branch->branch_name }} ({{ $voluntarySaving->branch->branch_code }})</td>
            </tr>
            <tr>
                <td><strong>Total Amount:</strong></td>
                <td colspan="3">
                    @php
                        $totalAmount = $voluntarySaving->deposits->sum('deposit_amount');
                        echo number_format($totalAmount, 2);
                    @endphp
                </td>
            </tr>
            <tr>
                <td><strong>Profit:</strong></td>
                <td colspan="3">{{ $voluntarySaving->profit ? number_format($voluntarySaving->profit, 2) : 'N/A' }}</td>
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

    <p>If you have any questions or need further assistance, please contact our branch office.</p>

    <p>Thank you for your trust in our services.</p>

    <div class="signature-box">
        <p>Authorized Signature</p>
        <p>Super Admin</p>
    </div>

    <div class="application-id">
        Application ID: {{ $voluntarySaving->id }}<br>
        Approval Date: {{ date('d/m/Y') }}<br>
        Generated on: {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>
