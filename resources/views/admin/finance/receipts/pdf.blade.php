<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <title>Payment Receipt</title>

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 13px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .school-name {
            font-size: 24px;
            font-weight: bold;
        }

        .receipt-title {
            font-size: 18px;
            margin-top: 5px;
        }

        .section {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            border: 1px solid #ccc;
            padding: 8px;
        }

        .text-right {
            text-align: right;
        }

        .total {
            font-weight: bold;
            background: #f2f2f2;
        }
    </style>
</head>

<body>

    <div class="header">

        @if(!empty($school->logo))
            <img src="{{ public_path($school->logo) }}" width="80">
        @endif

        <div class="school-name">
            {{ $school->name }}
        </div>

        <div>
            {{ $school->address ?? '' }}
        </div>

        <div class="receipt-title">
            PAYMENT RECEIPT
        </div>
    </div>

    <div class="section">

        <table>

            <tr>
                <td><strong>Receipt No</strong></td>
                <td>{{ $payment->receipt_no }}</td>

                <td><strong>Date</strong></td>
                <td>
                    {{ $payment->payment_date->format('d M Y') }}
                </td>
            </tr>

            <tr>
                <td><strong>Invoice No</strong></td>
                <td>{{ $payment->fee->invoice_no }}</td>

                <td><strong>Payment Method</strong></td>
                <td>{{ $payment->payment_method }}</td>
            </tr>

        </table>

    </div>

    <div class="section">

        <table>

            <tr>
                <td><strong>Student Name</strong></td>

                <td>
                    {{ optional($payment->fee->student->userprofile)->firstname }}
                    {{ optional($payment->fee->student->userprofile)->lastname }}
                </td>
            </tr>

            <tr>
                <td><strong>Class</strong></td>

                <td>
                    {{ optional($payment->fee->studentAcademic->standardLink->standard)->name }}
                    -
                    {{ optional($payment->fee->studentAcademic->standardLink->section)->name }}
                </td>
            </tr>

            <tr>
                <td><strong>Student ID</strong></td>

                <td>
                    {{ $payment->fee->user_id }}
                </td>
            </tr>

        </table>

    </div>

    <div class="section">

        <table>

            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>Payment Collected</td>

                    <td class="text-right">
                        Rs. {{ number_format($payment->amount, 2) }}
                    </td>
                </tr>

                <tr class="total">
                    <td>Total Paid</td>

                    <td class="text-right">
                        Rs. {{ number_format($payment->amount, 2) }}
                    </td>
                </tr>

            </tbody>

        </table>

    </div>

    <div class="section">

        <table>

            <tr>
                <td><strong>Invoice Total</strong></td>

                <td class="text-right">
                    Rs. {{ number_format($payment->fee->total_amount, 2) }}
                </td>
            </tr>

            <tr>
                <td><strong>Total Paid</strong></td>

                <td class="text-right">
                    Rs. {{ number_format($payment->fee->paid_amount, 2) }}
                </td>
            </tr>

            <tr>
                <td><strong>Balance</strong></td>

                <td class="text-right">
                    Rs. {{ number_format($payment->fee->balance, 2) }}
                </td>
            </tr>

            <tr>
                <td><strong>Advance Credit</strong></td>

                <td class="text-right">
                    Rs. {{ number_format($payment->fee->advance_amount ?? 0, 2) }}
                </td>
            </tr>

        </table>

    </div>

    <div style="
        margin-top: 60px;
        text-align: right;
    ">
        Authorized Signature
    </div>

</body>

</html>