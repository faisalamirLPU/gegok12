<?php

namespace App\Services\Finance;

class ConcessionService
{
    public function apply(float $amount, string $type, float $value): float
    {
        switch ($type) {

            case 'fixed':
                return max(0, $amount - $value);

            case 'percentage':
                return max(0, $amount - (($amount * $value) / 100));

            default:
                return $amount;
        }
    }
}
