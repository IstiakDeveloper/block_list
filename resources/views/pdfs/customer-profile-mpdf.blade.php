<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Customer Profile - {{ $customer->name }}</title>
    <style>
        /* Main styling */
        body {
            font-family: kalpurush, nikosh, sutonnymj, sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 14px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #4f46e5;
        }
        .customer-name {
            color: #4338ca;
            font-size: 24px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        h1 {
            color: #1f2937;
            font-size: 20px;
            margin-bottom: 5px;
        }
        .subtitle {
            color: #6b7280;
            font-size: 14px;
            margin-top: 0;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            color: #1f2937;
            font-size: 16px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        /* Table layouts work best in PDFs */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 8px;
            vertical-align: top;
        }
        .info-table tr {
            border-bottom: 1px solid #f3f4f6;
        }
        .detail-label {
            color: #6b7280;
            font-weight: bold;
            width: 30%;
        }
        .detail-value {
            color: #111827;
        }
        /* Two column layout for some sections */
        .two-column-table {
            width: 100%;
            border-collapse: collapse;
        }
        .two-column-table td {
            width: 50%;
            vertical-align: top;
            padding: 5px;
        }
        /* Cards for additional info */
        .info-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .info-title {
            color: #1f2937;
            font-size: 14px;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-content {
            color: #4b5563;
            min-height: 60px;
        }
        /* Footer styling */
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 10px;
        }
        /* NID images */
        .nid-img {
            max-width: 300px;
            max-height: 200px;
        }
        .created-at {
            color: #6b7280;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Customer Profile</h1>
        <div class="customer-name">{{ $customer->name }}</div>
        <p class="subtitle">Customer Detailed Profile</p>
        <p class="created-at">Created: {{ \Carbon\Carbon::parse($customer->created_at)->format('d/m/Y') }}</p>
    </div>

    <div class="section">
        <h2 class="section-title">Customer Information</h2>

        <table class="info-table">
            <tr>
                <td class="detail-label">Name (Bangla)</td>
                <td class="detail-value">{{ $customer->name_bn ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="detail-label">Father's Name</td>
                <td class="detail-value">{{ $customer->father_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="detail-label">Mother's Name</td>
                <td class="detail-value">{{ $customer->mother_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="detail-label">Spouse Name</td>
                <td class="detail-value">{{ $customer->spouse_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="detail-label">Date of Birth</td>
                <td class="detail-value">{{ $customer->dob ? \Carbon\Carbon::parse($customer->dob)->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="detail-label">NID Number</td>
                <td class="detail-value">{{ $customer->nid_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="detail-label">Phone Number</td>
                <td class="detail-value">{{ $customer->phone_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="detail-label">Branch</td>
                <td class="detail-value">{{ $customer->branch ? $customer->branch->branch_name : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="detail-label">Created By</td>
                <td class="detail-value">{{ $customer->user ? $customer->user->name : 'N/A' }}</td>
            </tr>
        </table>
    </div>

    @if($customer->nid_part_1 || $customer->nid_part_2)
    <div class="section">
        <h2 class="section-title">NID Documents</h2>

        <table class="two-column-table">
            <tr>
                @if($customer->nid_part_1)
                <td>
                    <div class="info-card">
                        <h3 class="info-title">NID Part 1</h3>
                        <img src="{{ public_path('storage/' . $customer->nid_part_1) }}" alt="NID Part 1" class="nid-img" onerror="this.style.display='none'">
                    </div>
                </td>
                @endif

                @if($customer->nid_part_2)
                <td>
                    <div class="info-card">
                        <h3 class="info-title">NID Part 2</h3>
                        <img src="{{ public_path('storage/' . $customer->nid_part_2) }}" alt="NID Part 2" class="nid-img" onerror="this.style.display='none'">
                    </div>
                </td>
                @endif
            </tr>
        </table>
    </div>
    @endif

    <div class="section">
        <h2 class="section-title">Additional Information</h2>

        <table class="two-column-table">
            <tr>
                <td>
                    <div class="info-card">
                        <h3 class="info-title">Address Information</h3>
                        <div class="info-content">
                            {{ $customer->address ?? 'No address provided' }}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="info-card">
                        <h3 class="info-title">Details</h3>
                        <div class="info-content">
                            {{ $customer->details ?? 'No additional details' }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>This document was generated on {{ date('d/m/Y') }} at {{ date('H:i:s') }}</p>
        <p><span style="font-family: DejaVu Sans, sans-serif;">&copy;</span> {{ date('Y') }} Mousumi NGO. All rights reserved.</p>
    </div>
</body>
</html>
