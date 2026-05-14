<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Parent Account Statement</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #222; padding-bottom: 10px; }
        .school-name { font-size: 20px; font-weight: bold; }
        .school-meta { font-size: 10px; color: #555; }
        .title { font-size: 16px; font-weight: bold; text-align: center; margin: 15px 0; }
        
        .student-info { margin-bottom: 20px; }
        .student-info table { width: 100%; border: none; }
        .student-info td { padding: 4px; border: none; }
        
        .summary { display: table; width: 100%; margin-bottom: 20px; }
        .summary-item { display: table-cell; width: 25%; text-align: center; border: 1px solid #ddd; padding: 10px; background: #f9f9f9; }
        .summary-label { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #555; }
        .summary-value { font-size: 14px; font-weight: bold; margin-top: 5px; }
        
        .ledger { width: 100%; border-collapse: collapse; }
        .ledger th, .ledger td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .ledger th { background: #eee; font-size: 11px; }
        .text-right { text-align: right; }
        .amount { font-weight: bold; }
        
        .type-invoice { background: #fff; }
        .type-payment { background: #f0fdf4; }
        .type-advance { background: #eff6ff; }
    </style>
</head>
<body>

    <div class="header">
        <div class="school-name">{{ $school->name }}</div>
        <div class="school-meta">
            {{ $school->address ?? '' }} | Phone: {{ $school->phone ?? '' }} | Email: {{ $school->email ?? '' }}
        </div>
    </div>

    <div class="title">ACCOUNT STATEMENT</div>

    <div class="student-info">
        <table>
            <tr>
                <td><strong>Student Name:</strong> {{ optional($student->userprofile)->firstname }} {{ optional($student->userprofile)->lastname }}</td>
                <td><strong>Student ID:</strong> #{{ $student->id }}</td>
            </tr>
            <tr>
                <td><strong>Class:</strong> {{ optional(optional($student->studentAcademic)->standardLink)->standard->name ?? '-' }} - {{ optional(optional($student->studentAcademic)->standardLink)->section->name ?? '-' }}</td>
                <td><strong>Date Generated:</strong> {{ now()->format('d M Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="summary-label">Total Billed</div>
            <div class="summary-value">Rs. {{ number_format($statement['summary']['total_billed'], 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Paid</div>
            <div class="summary-value" style="color: green;">Rs. {{ number_format($statement['summary']['total_paid'], 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Pending</div>
            <div class="summary-value" style="color: red;">Rs. {{ number_format($statement['summary']['total_pending'], 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Advance Credit</div>
            <div class="summary-value" style="color: blue;">Rs. {{ number_format($statement['summary']['advance_balance'], 2) }}</div>
        </div>
    </div>

    <table class="ledger">
        <thead>
            <tr>
                <th width="15%">Date</th>
                <th width="45%">Description</th>
                <th width="20%" class="text-right">Charge (Dr)</th>
                <th width="20%" class="text-right">Payment (Cr)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statement['invoices'] as $invoice)
            <tr class="type-invoice">
                <td>{{ $invoice->created_at->format('d M Y') }}</td>
                <td>
                    Invoice Generated: {{ $invoice->invoice_no }}
                    <br><small>{{ $invoice->payment_period ? \Carbon\Carbon::createFromFormat('Y-m', $invoice->payment_period)->format('M Y') : '' }}</small>
                </td>
                <td class="text-right amount">Rs. {{ number_format($invoice->total_amount, 2) }}</td>
                <td class="text-right">-</td>
            </tr>
            @endforeach

            @foreach($statement['payments'] as $payment)
            <tr class="type-payment">
                <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : $payment->created_at->format('d M Y') }}</td>
                <td>
                    Payment Received: {{ $payment->receipt_no }} ({{ ucfirst($payment->payment_method) }})
                    <br><small>Against: {{ optional($payment->fee)->invoice_no }}</small>
                </td>
                <td class="text-right">-</td>
                <td class="text-right amount" style="color: green;">Rs. {{ number_format($payment->amount, 2) }}</td>
            </tr>
            @endforeach

            @foreach($statement['ledgers'] as $ledger)
            @if($ledger->transaction_type == \App\Models\StudentFeeLedger::TYPE_ADVANCE_APPLIED)
            <tr class="type-advance">
                <td>{{ \Carbon\Carbon::parse($ledger->created_at)->format('d M Y') }}</td>
                <td>
                    Advance Applied
                    <br><small>{{ $ledger->remarks }}</small>
                </td>
                <td class="text-right">-</td>
                <td class="text-right amount" style="color: blue;">Rs. {{ number_format($ledger->amount, 2) }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: right; font-weight: bold;">
        Authorized Signature
    </div>

</body>
</html>
