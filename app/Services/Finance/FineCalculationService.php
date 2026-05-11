<?php

namespace App\Services\Finance;

use Carbon\Carbon;

class FineCalculationService
{
    public function calculate(
        string $fineType,
        float $amount,
        Carbon $dueDate,
        ?float $maxLimit = null,
        int $graceDays = 0
    ): float {

        $today = now();

        if ($today->lte($dueDate->copy()->addDays($graceDays))) {
            return 0;
        }

        $lateDays = $today->diffInDays($dueDate);

        $fine = 0;

        switch ($fineType) {

            case 'fixed':
                $fine = $amount;
                break;

            case 'percentage':
                $fine = ($amount / 100);
                break;

            case 'daily':
                $fine = $amount * $lateDays;
                break;
        }

        if ($maxLimit && $fine > $maxLimit) {
            $fine = $maxLimit;
        }

        return round($fine, 2);
    }
}
