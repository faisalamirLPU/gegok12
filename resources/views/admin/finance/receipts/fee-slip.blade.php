<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <title>
        Student Fee Slip
    </title>

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
            color: #222;
        }

        .container {
            border: 1px solid #222;
            padding: 18px;
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

        .header-table td {
            border: none;
            vertical-align: top;
        }

        .school-name {
            font-size: 24px;
            font-weight: bold;
        }

        .school-meta {
            font-size: 11px;
            line-height: 18px;
            color: #444;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            border-top: 2px solid #222;
            border-bottom: 2px solid #222;
            padding: 8px;
        }

        .section {
            margin-top: 20px;
        }

        .text-right {
            text-align: right;
        }

        .total {
            background: #f3f3f3;
            font-weight: bold;
        }

        .status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
        }

        .paid {
            background: #d1fae5;
        }

        .partial {
            background: #fef3c7;
        }

        .pending {
            background: #fee2e2;
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

    <div class="container">

        {{-- Header --}}
        <table class="header-table">

            <tr>

                <td width="100">

                    @if(
                            !empty($schoolDetails['school_logo']) &&
                            file_exists(public_path($schoolDetails['school_logo']))
                        )

                        <img src="{{ public_path($schoolDetails['school_logo']) }}" width="80">

                    @endif

                </td>

                <td>

                    <div class="school-name">
                        {{ $school->name }}
                    </div>

                    <div class="school-meta">

                        {{ $school->address }}

                        <br>

                        Phone:
                        {{ $school->phone }}

                        |

                        Email:
                        {{ $school->email }}

                        <br>

                        @if(!empty($schoolDetails['board']))
                            Board:
                            {{ $schoolDetails['board'] }}
                        @endif

                        @if(!empty($schoolDetails['affiliation_no']))
                            |
                            Affiliation:
                            {{ $schoolDetails['affiliation_no'] }}
                        @endif

                    </div>

                </td>

            </tr>

        </table>

        {{-- Title --}}
        <div class="title">
            STUDENT FEE SLIP
        </div>

        {{-- Student Info --}}
        <div class="section">

            <table>

                <tr>

                    <td width="25%">
                        <strong>Student Name</strong>
                    </td>

                    <td width="25%">

                        {{ optional($fee->student->userprofile)->firstname }}

                        {{ optional($fee->student->userprofile)->lastname }}

                    </td>

                    <td width="25%">
                        <strong>Invoice No</strong>
                    </td>

                    <td width="25%">
                        {{ $fee->invoice_no }}
                    </td>

                </tr>

                <tr>

                    <td>
                        <strong>Class</strong>
                    </td>

                    <td>

                        {{ optional($fee->studentAcademic->standardLink->standard)->name }}

                        -

                        {{ optional($fee->studentAcademic->standardLink->section)->name }}

                    </td>

                    <td>
                        <strong>Due Date</strong>
                    </td>

                    <td>

                        {{ optional($fee->due_date)->format('d M Y') }}

                    </td>

                </tr>

                <tr>

                    <td>
                        <strong>Payment Period</strong>
                    </td>

                    <td>

                        {{ $fee->display_period ?? '-' }}

                    </td>

                    <td>
                        <strong>Status</strong>
                    </td>

                    <td>

                        <span class="status
                        @if($fee->erp_status == 'Paid')
                            paid
                        @elseif($fee->erp_status == 'Partial')
                            partial
                        @else
                            pending
                        @endif
                    ">
                            {{ strtoupper($fee->erp_status) }}
                        </span>

                    </td>

                </tr>

            </table>

        </div>

        {{-- Fee Breakdown --}}
        <div class="section">

            <table>

                <thead>

                    <tr>

                        <th>
                            Fee Component
                        </th>

                        <th width="150" class="text-right">
                            Amount
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($fee->items as $item)

                        <tr>

                            <td>

                                {{ $item->remarks ?: optional($item->category)->name }}

                            </td>

                            <td class="text-right">

                                Rs.
                                {{ number_format($item->total, 2) }}

                            </td>

                        </tr>

                    @endforeach

                    <tr class="total">

                        <td>
                            Total Amount
                        </td>

                        <td class="text-right">

                            Rs.
                            {{ number_format($fee->total_amount, 2) }}

                        </td>

                    </tr>

                    <tr>

                        <td>
                            Paid Amount
                        </td>

                        <td class="text-right">

                            Rs.
                            {{ number_format($fee->paid_amount, 2) }}

                        </td>

                    </tr>

                    <tr>

                        <td>
                            Balance Due
                        </td>

                        <td class="text-right">

                            Rs.
                            {{ number_format($fee->balance_amount, 2) }}

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        {{-- Footer --}}
        <div style="
        margin-top: 50px;
        text-align: right;
    ">

            Authorized Signature

        </div>

    </div>

</body>

</html>