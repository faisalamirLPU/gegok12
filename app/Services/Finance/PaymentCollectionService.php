<?php

namespace App\Services\Finance;

use Illuminate\Support\Facades\DB;

class PaymentCollectionService
{
    public function collect(array $data): array
    {
        return DB::transaction(function () use ($data) {

            /*
            Future Logic:

            - validate invoice
            - apply partial payment
            - allocate payment
            - generate receipt
            - ledger entry
            - update invoice balance
            */

            return [];
        });
    }
}
