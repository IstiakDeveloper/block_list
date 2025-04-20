<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Voluntary Savings Withdrawal Application Form</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            position: relative;
            font-size: 12px;
        }

        .container {
            width: 21cm;
            height: 29cm;
            padding: 0.5cm 1.5cm 0.5cm 0.8cm;
            margin-left: -30px;
            margin-top: -30px;
            background: white;
            position: relative;
            overflow: hidden;
        }

        .content {
            padding: 0 40px;
        }

        .header {
            margin-bottom: 10px;
            padding: 0;
            max-width: 100%;
            overflow: hidden;
        }

        .header img {
            width: 100%;
            height: auto;
            display: block;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            opacity: 0.1;
            font-size: 90px;
            font-weight: bold;
            color: #000;
            z-index: 0;
            pointer-events: none;
            text-transform: uppercase;
        }

        h1 {
            text-align: center;
            margin: 8px 0;
            color: #333;
            font-size: 20px;
        }

        .date-section {
            text-align: right;
            margin-bottom: 10px;
            padding-right: 0;
            margin-right: 0;
        }

        .address-section {
            margin-bottom: 12px;
        }

        .subject-section {
            font-weight: bold;
            margin-bottom: 8px;
            text-decoration: underline;
        }

        .content-section {
            margin-bottom: 10px;
            text-align: justify;
            padding-right: 0;
            margin-right: 0;
        }

        .account-details {
            margin: 10px 0;
            border: 1px solid #ddd;
            padding: 8px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .account-details h3 {
            margin-top: 0;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .account-details ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .account-details li {
            padding: 2px 0;
        }

        .signature-table {
            width: 100%;
            margin-top: 30px;
            border: none;
        }

        .signature-table td {
            width: 50%;
            vertical-align: top;
            border: none;
        }

        .signature-left {
            text-align: left;
        }

        .signature-right {
            text-align: right;
        }

        .signature-img {
            height: 45px;
            margin: 4px 0;
        }

        .application-id {
            font-size: 9px;
            color: #666;
            margin-top: 20px;
            position: absolute;
            bottom: 8px;
            left: 1cm;
        }

        p {
            margin: 4px 0;
        }

        /* Table styles */
        .deposit-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .deposit-table th,
        .deposit-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
        }

        .deposit-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .deposit-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .total-row {
            font-weight: bold;
            background-color: #eaeaea !important;
        }

        @media print {
            .container {
                width: 100%;
                height: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        @if($voluntarySaving->status == 'approved')
        <div class="watermark">APPROVED</div>
        @elseif($voluntarySaving->status == 'rejected')
        <div class="watermark">REJECTED</div>
        @else
        <div class="watermark">PENDING</div>
        @endif

        <div class="header">
            <img src="{{ public_path('header.jpg') }}" alt="Header">
        </div>
        <div class="content">
            <div class="date-section">
                <p style="white-space: nowrap;">Date: {{ date('d/m/Y', strtotime($voluntarySaving->application_date)) }}
                </p>
            </div>
            <div class="address-section">
                <p>To<br>
                    Deputy Executive Director<br>
                    Moushumi, Ukilpara, Naogaon.</p>
            </div>
            <div class="subject-section">
                <p>Subject: Regarding Voluntary Savings Withdrawal.</p>
            </div>
            <div class="content-section">
                <p>Sir,</p>
                <p>With due respect, I would like to state that {{ $voluntarySaving->member_name }}
                    ({{ $voluntarySaving->member_code }}) is a member of {{ $voluntarySaving->somiti_name }}
                    ({{ $voluntarySaving->somiti_code }}) Cooperative of Moushumi {{ $voluntarySaving->branch->branch_name }} Branch.
                    The member has deposited voluntary savings in their account, the details of which are provided in the table below. Currently, the member needs to withdraw this savings due to family requirements.
                    Member's mobile: {{ $voluntarySaving->member_mobile ?? 'Not applicable' }}</p>
            </div>

            <div class="account-details">
                <h3>Voluntary Savings Details:</h3>
                <table class="deposit-table">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Date</th>
                            <th>Account Name</th>
                            <th>Deposit Amount</th>
                            <th>Profit</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalDeposit = 0;
                            $totalProfit = 0;
                            $grandTotal = 0;
                        @endphp

                        @foreach ($voluntarySaving->deposits as $key => $deposit)
                            @php
                                $depositAmount = $deposit->deposit_amount;
                                $profit = $deposit->profit ?? 0;
                                $rowTotal = $depositAmount + $profit;

                                $totalDeposit += $depositAmount;
                                $totalProfit += $profit;
                                $grandTotal += $rowTotal;
                            @endphp
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ date('d/m/Y', strtotime($deposit->deposit_date)) }}</td>
                                <td>{{ $voluntarySaving->member_name }}</td>
                                <td>{{ number_format($depositAmount, 0) }}/-</td>
                                <td>{{ number_format($profit, 0) }}/-</td>
                                <td>{{ number_format($rowTotal, 0) }}/-</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="3">Grand Total</td>
                            <td>{{ number_format($totalDeposit, 0) }}/-</td>
                            <td>{{ number_format($totalProfit, 0) }}/-</td>
                            <td>{{ number_format($grandTotal, 0) }}/-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="content-section">
                <p>Therefore, I kindly request you to consider this matter and grant permission for the withdrawal of the member's voluntary savings.</p>
            </div>

            <!-- Using a table layout for signatures instead of flex -->
            <table class="signature-table">
                <tr>
                    <td class="signature-left">
                        <p>Submitted by,</p>
                        @if ($voluntarySaving->signature)
                            <img class="signature-img" src="{{ public_path('storage/' . $voluntarySaving->signature) }}"
                                alt="Signature">
                        @else
                            <br>
                        @endif
                        <p>{{ $voluntarySaving->applicant_name }}<br>
                            {{ $voluntarySaving->designation }}
                            @if ($voluntarySaving->pin)
                                PIN-{{ $voluntarySaving->pin }}
                            @endif
                            <br>
                            {{ $voluntarySaving->branch->branch_name }} Branch
                        </p>
                    </td>
                    @if($voluntarySaving->status == 'approved')
                    <td class="signature-left">
                        <p>Approved by,</p>
                        <img class="signature-img" src="{{ public_path('signature.jpg') }}" alt="Approval Signature">
                        <p>Erfan Ali<br>
                           Deputy Executive Director<br>
                           Mousumi, Ukilpara, Naogaon.
                        </p>
                    </td>
                    @else
                    <td></td>
                    @endif
                </tr>
            </table>

            <div class="application-id">
                Application ID: VS-{{ $voluntarySaving->id }} | Created on: {{ date('d/m/Y H:i:s') }}
            </div>
        </div>
    </div>
</body>
</html>
