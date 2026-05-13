<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <title>
        Payment Receipt
    </title>

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 13px;
            color: #222;
        }

        .header {
            margin-bottom: 20px;
        }

        .header-table td {
            border: none;
            vertical-align: top;
        }

        .school-name {
            font-size: 24px;
            font-weight: bold;
        }

        .school-details {
            font-size: 11px;
            line-height: 18px;
            margin-top: 3px;
            color: #444;
        }

        .receipt-title {
            font-size: 18px;
            margin-top: 10px;
            font-weight: bold;
            text-align: center;
            border-top: 2px solid #222;
            border-bottom: 2px solid #222;
            padding: 8px 0;
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

        th {
            background: #f2f2f2;
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

    @php

        $schoolDetails = \App\Models\SchoolDetail::where(
            'school_id',
            $school->id
        )->pluck(
                'meta_value',
                'meta_key'
            );

    @endphp

    {{-- Header --}}
    <div class="header">

        <table class="header-table">

            <tr>

                {{-- Logo --}}
                <td width="100">

                    @if(
                            !empty($schoolDetails['school_logo']) &&
                            file_exists(public_path($schoolDetails['school_logo']))
                        )

                        <img src="{{ public_path($schoolDetails['school_logo']) }}" width="80">

                    @endif

                </td>

                {{-- School Details --}}
                <td>

                    <div class="school-name">
                        {{ $school->name }}
                    </div>

                    <div class="school-details">

                        {{-- Address --}}
                        {{ $school->address ?? '' }}

                        <br>

                        {{-- Phone --}}
                        @if(!empty($school->phone))
                            Phone:
                            {{ $school->phone }}
                        @endif

                        {{-- Email --}}
                        @if(!empty($school->email))
                            |
                            Email:
                            {{ $school->email }}
                        @endif

                        <br>

                        {{-- Board --}}
                        @if(!empty($schoolDetails['board']))
                            Board:
                            {{ $schoolDetails['board'] }}
                        @endif

                        {{-- Affiliation --}}
                        @if(!empty($schoolDetails['affiliation_no']))
                            |
                            Affiliation No:
                            {{ $schoolDetails['affiliation_no'] }}
                        @endif

                        <br>

                        {{-- Website --}}
                        @if(!empty($schoolDetails['website']))
                            Website:
                            {{ $schoolDetails['website'] }}
                        @endif

                        {{-- Motto --}}
                        @if(!empty($schoolDetails['moto']))

                            <br>

                            <em>
                                "{{ $schoolDetails['moto'] }}"
                            </em>

                        @endif

                    </div>

                </td>

            </tr>

        </table>

        {{-- Receipt Title --}}
        <div class="receipt-title">
            PAYMENT RECEIPT
        </div>

    </div>

    {{-- Receipt Information --}}
    <div class="section">

        <table>

            <tr>

                <td>
                    <strong>Receipt No</strong>
                </td>

                <td>
                    {{ $payment->receipt_no }}
                </td>

                <td>
                    <strong>Date</strong>
                </td>

                <td>

                    {{ optional($payment->payment_date)->format('d M Y') ?? now()->format('d M Y') }}

                </td>

            </tr>

            <tr>

                <td>
                    <strong>Invoice No</strong>
                </td>

                <td>
                    {{ $payment->fee->invoice_no }}
                </td>

                <td>
                    <strong>Payment Method</strong>
                </td>

                <td>
                    {{ strtoupper($payment->payment_method) }}
                </td>

            </tr>

        </table>

    </div>

    {{-- Student Details --}}
    <div class="section">

        <table>

            <tr>

                <td width="30%">
                    <strong>Student Name</strong>
                </td>

                <td>

                    {{ optional($payment->fee->student->userprofile)->firstname }}

                    {{ optional($payment->fee->student->userprofile)->lastname }}

                </td>

            </tr>

            <tr>

                <td>
                    <strong>Class</strong>
                </td>

                <td>

                    {{ optional($payment->fee->studentAcademic->standardLink->standard)->name }}

                    -

                    {{ optional($payment->fee->studentAcademic->standardLink->section)->name }}

                </td>

            </tr>

            <tr>

                <td>
                    <strong>Student ID</strong>
                </td>

                <td>
                    {{ $payment->fee->user_id }}
                </td>

            </tr>

            <tr>

                <td>
                    <strong>Payment Period</strong>
                </td>

                <td>

                    @if(!empty($payment->fee->payment_period))

                                        {{ \Carbon\Carbon::createFromFormat(
                            'Y-m',
                            $payment->fee->payment_period
                        )->format('F Y') }}

                    @else

                        N/A

                    @endif

                </td>

            </tr>

        </table>

    </div>

    {{-- Payment Details --}}
    <div class="section">

        <table>

            <thead>

                <tr>

                    <th>
                        Description
                    </th>

                    <th class="text-right">
                        Amount
                    </th>

                </tr>

            </thead>

            <tbody>

                <tr>

                    <td>
                        Payment Collected
                    </td>

                    <td class="text-right">

                        Rs.
                        {{ number_format($payment->amount, 2) }}

                    </td>

                </tr>

                @if(($payment->fee->advance_amount ?? 0) > 0)

                                <tr>

                                    <td>
                                        Advance Credit Used
                                    </td>

                                    <td class="text-right">

                                        Rs.
                                        {{ number_format(
                        $payment->fee->advance_amount,
                        2
                    ) }}

                                    </td>

                                </tr>

                @endif

                <tr class="total">

                    <td>
                        Total Paid
                    </td>

                    <td class="text-right">

                        Rs.
                        {{ number_format($payment->amount, 2) }}

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

    {{-- Invoice Summary --}}
    <div class="section">

        <table>

            <tr>

                <td>
                    <strong>Invoice Total</strong>
                </td>

                <td class="text-right">

                    Rs.
                    {{ number_format(
    $payment->fee->total_amount,
    2
) }}

                </td>

            </tr>

            @if($payment->fee->status == \App\Models\Fee::STATUS_ADVANCE || $payment->fee->advance_credit > 0)
                <tr>
                    <td><strong>Paid Against Invoice</strong></td>
                    <td class="text-right">
                        Rs. {{ number_format($payment->fee->total_amount, 2) }}
                    </td>
                </tr>
            @else
                <tr>
                    <td><strong>Total Paid</strong></td>
                    <td class="text-right">
                        Rs. {{ number_format($payment->fee->paid_amount, 2) }}
                    </td>
                </tr>
            @endif

            <tr>

                <td>
                    <strong>Balance</strong>
                </td>

                <td class="text-right">

                    Rs.
                    {{ number_format(
    $payment->fee->balance,
    2
) }}

                </td>

            </tr>

            <tr>

                <td>
                    <strong>Advance Credit</strong>
                </td>

                <td class="text-right">

                    Rs.
                    {{ number_format(
    $payment->fee->advance_amount ?? 0,
    2
) }}

                </td>

            </tr>

        </table>

    </div>

    {{-- Signature --}}
    <div style="
    margin-top: 60px;
    text-align: right;
">

        Authorized Signature

    </div>

</body>

</html>