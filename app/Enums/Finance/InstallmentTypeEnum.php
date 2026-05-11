<?php

namespace App\Enums\Finance;

class InstallmentTypeEnum
{
    public const MONTHLY = 'monthly';

    public const QUARTERLY = 'quarterly';

    public const HALF_YEARLY = 'half_yearly';

    public const YEARLY = 'yearly';

    public const CUSTOM = 'custom';

    public static function values(): array
    {
        return [
            self::MONTHLY,
            self::QUARTERLY,
            self::HALF_YEARLY,
            self::YEARLY,
            self::CUSTOM,
        ];
    }
}
