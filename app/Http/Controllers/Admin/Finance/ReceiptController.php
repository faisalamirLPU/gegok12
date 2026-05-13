<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\Fee;

class ReceiptController extends Controller
{
    public function index(Fee $fee)
    {
        $fee->load('payments');

        return view(
            'admin.finance.receipts.index',
            compact('fee')
        );
    }
}
