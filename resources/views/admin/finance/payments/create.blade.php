@extends('layouts.admin.layout')

@section('content')

<div class="container">

    <h2>Collect Payment</h2>

    <div class="card p-4">

        <h4>
            Invoice:
            {{ $fee->invoice_no }}
        </h4>

        <h5>
            Balance:
            ₹{{ number_format($fee->balance, 2) }}
        </h5>

        <form
            method="POST"
            action="{{ route('finance.payments.store', $fee->id) }}"
        >

            @csrf

            <div class="mb-3">

                <label>Amount</label>

                <input
                    type="number"
                    step="0.01"
                    name="amount"
                    class="form-control"
                    required
                >

            </div>

            <div class="mb-3">

                <label>Payment Method</label>

                <select
                    name="payment_method"
                    class="form-control"
                >

                    <option value="cash">
                        Cash
                    </option>

                    <option value="upi">
                        UPI
                    </option>

                    <option value="bank">
                        Bank
                    </option>

                </select>

            </div>

            <div class="mb-3">

                <label>Transaction ID</label>

                <input
                    type="text"
                    name="transaction_id"
                    class="form-control"
                >

            </div>

            <div class="mb-3">

                <label>Remarks</label>

                <textarea
                    name="remarks"
                    class="form-control"
                ></textarea>

            </div>

            <button
                type="submit"
                class="btn btn-success"
            >
                Submit Payment
            </button>

        </form>

    </div>

</div>

@endsection
