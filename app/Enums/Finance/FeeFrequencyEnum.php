<?php

namespace App\Enums\Finance;

class FeeFrequencyEnum
{
    public const ONE_TIME = 'one_time';

    public const MONTHLY = 'monthly';

    public const QUARTERLY = 'quarterly';

    public const HALF_YEARLY = 'half_yearly';

    public const YEARLY = 'yearly';

    public static function values(): array
    {
        return [
            self::ONE_TIME,
            self::MONTHLY,
            self::QUARTERLY,
            self::HALF_YEARLY,
            self::YEARLY,
        ];
    }
}
