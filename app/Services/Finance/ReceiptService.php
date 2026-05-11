<?php

namespace App\Services\Finance;

class ReceiptService
{
    public function generateReceiptNumber(): string
    {
        return 'RCPT-' . now()->format('YmdHis') . rand(100, 999);
    }
}
