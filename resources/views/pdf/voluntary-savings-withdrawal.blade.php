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

        /* Basic watermark */
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

        /* Enhanced rejected watermark */
        .rejected-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            opacity: 0.2;
            font-size: 90px;
            font-weight: bold;
            color: #FF0000;
            z-index: 0;
            pointer-events: none;
            text-transform: uppercase;
        }

        /* Rejection cross overlay */
        .rejection-cross {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 5;
            pointer-events: none;
        }

        .rejection-cross:before,
        .rejection-cross:after {
            content: "";
            position: absolute;
            background-color: #FF0000;
            opacity: 0.2;
        }

        .rejection-cross:before {
            top: 0;
            left: 50%;
            width: 5px;
            height: 100%;
            transform: translateX(-50%);
        }

        .rejection-cross:after {
            top: 50%;
            left: 0;
            width: 100%;
            height: 5px;
            transform: translateY(-50%);
        }

        /* Rejection border */
        .rejection-border {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 10px solid #FF0000;
            opacity: 0.3;
            z-index: 4;
            pointer-events: none;
        }

        /* Rejection stamp */
        .rejection-stamp {
            position: absolute;
            top: 35%;
            right: 10%;
            transform: rotate(15deg);
            z-index: 6;
            pointer-events: none;
        }

        .stamp {
            padding: 8px 15px;
            background-color: #FF0000;
            opacity: 0.8;
            color: white;
            font-weight: bold;
            border: 4px solid #990000;
            border-radius: 6px;
            font-size: 24px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
            transform: rotate(-15deg);
        }

        /* Enhanced approved watermark and security features */
        .approved-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            opacity: 0.12;
            font-size: 90px;
            font-weight: bold;
            color: #006400;
            /* Dark green */
            z-index: 0;
            pointer-events: none;
            text-transform: uppercase;
        }

        /* Security pattern for approved documents */
        .security-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: repeating-linear-gradient(45deg,
                    rgba(0, 100, 0, 0.02),
                    rgba(0, 100, 0, 0.02) 10px,
                    rgba(0, 0, 0, 0.01) 10px,
                    rgba(0, 0, 0, 0.01) 20px);
            z-index: 1;
            pointer-events: none;
        }

        /* Digital seal/hologram effect */
        .approval-seal {
            position: absolute;
            top: 50%;
            left: 50%;
            margin-left: -50px;
            /* half of width */
            margin-top: -50px;
            /* half of height */
            width: 100px;
            height: 100px;
            z-index: 10;
            pointer-events: none;
            background: linear-gradient(135deg, rgba(0, 191, 255, 0.7), rgba(30, 144, 255, 0.7), rgba(65, 105, 225, 0.7), rgba(0, 0, 205, 0.7));
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            opacity: 0.7;
            transform: rotate(-15deg);
        }

        .approval-text {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            text-align: center;
            margin-top: -7px;
            /* half of font size */
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        /* Micro text border for approved documents */
        .micro-text-border {
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 1px solid #ccc;
            z-index: 2;
            pointer-events: none;
            overflow: hidden;
        }

        .micro-text-border:before {
            content: "MOUSHUMI OFFICIAL DOCUMENT VERIFIED APPROVED NOT TO BE DUPLICATED ";
            position: absolute;
            font-size: 3px;
            line-height: 4px;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            color: #006400;
            opacity: 0.5;
            white-space: repeat;
        }

        /* QR code placeholder */
        .qr-code {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 80px;
            height: 80px;
            background-color: white;
            padding: 5px;
            border: 1px solid #ddd;
            z-index: 3;
        }

        /* Approved border */
        .approved-border {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 8px solid #006400;
            opacity: 0.2;
            z-index: 2;
            pointer-events: none;
        }

        /* Verification text */
        .verification-text {
            position: absolute;
            bottom: 30px;
            left: 120px;
            font-size: 10px;
            color: #006400;
            opacity: 0.7;
            z-index: 3;
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
            border-collapse: collapse;
        }

        .signature-table td {
            vertical-align: top;
            border: none;
        }

        .signature-left {
            width: 50%;
            text-align: left;
        }

        .signature-right {
            width: 50%;
            text-align: right;
        }

        .signature-img {
            height: 45px;
            margin: 4px 0;
        }

        /* Additional style to ensure proper spacing */
        .signature-table p {
            margin: 5px 0;
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

        /* Rejection reason section */
        .rejection-reason {
            margin: 15px 0;
            padding: 10px;
            border: 1px dashed #FF0000;
            background-color: #FFF0F0;
            border-radius: 5px;
        }

        .rejection-reason h3 {
            color: #FF0000;
            margin-top: 0;
            margin-bottom: 5px;
        }

        .rejection-reason p {
            margin: 5px 0;
            color: #333;
        }

        /* Approval-specific elements */
        .approval-reason {
            margin: 15px 0;
            padding: 10px;
            border: 1px dashed #006400;
            background-color: #F0FFF0;
            border-radius: 5px;
        }

        .approval-reason h3 {
            color: #006400;
            margin-top: 0;
            margin-bottom: 5px;
        }

        .approval-reason p {
            margin: 5px 0;
            color: #333;
        }

        /* Guilloche pattern simulation for background security */
        .guilloche-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: repeating-radial-gradient(circle at 50% 50%,
                    rgba(0, 100, 0, 0.02) 0px,
                    rgba(0, 100, 0, 0.02) 2px,
                    transparent 2px,
                    transparent 4px);
            z-index: 1;
            pointer-events: none;
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
        @if ($voluntarySaving->status == 'approved')
            <div class="approved-watermark">APPROVED</div>
            <div class="security-pattern"></div>
            <div class="guilloche-pattern"></div>
            <div class="approved-border"></div>
            <div class="micro-text-border"></div>
            <div class="approval-seal">
                <div class="approval-text">VERIFIED</div>
            </div>
        @elseif($voluntarySaving->status == 'rejected')
            <div class="rejected-watermark">✕ REJECTED ✕</div>
            <div class="rejection-cross"></div>
            <div class="rejection-border"></div>
            <div class="rejection-stamp">
                <div class="stamp">REJECTED</div>
            </div>
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
                    ({{ $voluntarySaving->somiti_code }}) Cooperative of Moushumi
                    {{ $voluntarySaving->branch->branch_name }} Branch.
                    The member has deposited voluntary savings in their account, the details of which are provided in
                    the table below. Currently, the member needs to withdraw this savings due to family requirements.
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
                <p>Therefore, I kindly request you to consider this matter and grant permission for the withdrawal of
                    the member's voluntary savings.</p>
            </div>

            @if ($voluntarySaving->status == 'rejected')
                <div class="rejection-reason">
                    <h3>Application Rejected</h3>
                    <p><strong>Reason for Rejection:</strong>
                        {{ $voluntarySaving->rejection_reason ?? 'Insufficient documentation or information provided.' }}
                    </p>
                    <p><strong>Rejected Date:</strong>
                        {{ date('d/m/Y', strtotime($voluntarySaving->rejection_date ?? $voluntarySaving->updated_at)) }}
                    </p>
                    <p><strong>Rejected By:</strong> {{ $voluntarySaving->rejected_by ?? 'Deputy Executive Director' }}
                    </p>
                </div>
            @endif

            <table class="signature-table">
                <tr>
                    <td class="signature-left">
                        <p>Submitted by,</p>
                        @if ($voluntarySaving->signature)
                            <img class="signature-img"
                                src="{{ public_path('storage/' . $voluntarySaving->signature) }}" alt="Signature">
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
                    <td class="signature-right">
                        @if ($voluntarySaving->status == 'approved')
                            <p>Approved by,</p>
                            <img class="signature-img" src="{{ public_path('signature.jpg') }}"
                                alt="Approval Signature">
                            <p>Erfan Ali<br>
                                Deputy Executive Director<br>
                                Mousumi, Ukilpara, Naogaon.
                            </p>
                        @elseif ($voluntarySaving->status == 'rejected')
                            <p>Rejected by,</p>
                            <img class="signature-img" src="{{ public_path('signature.jpg') }}"
                                alt="Rejection Signature">
                            <p>Erfan Ali<br>
                                Deputy Executive Director<br>
                                Mousumi, Ukilpara, Naogaon.
                            </p>
                        @endif
                    </td>
                </tr>
            </table>

            <div class="application-id">
                Application ID: VS-{{ $voluntarySaving->id }} | Created on: {{ date('d/m/Y H:i:s') }}
            </div>
        </div>
    </div>
</body>

</html>
