<?php

namespace App\Enums\Finance;

class FineTypeEnum
{
    public const FIXED = 'fixed';

    public const PERCENTAGE = 'percentage';

    public const DAILY = 'daily';

    public static function values(): array
    {
        return [
            self::FIXED,
            self::PERCENTAGE,
            self::DAILY,
        ];
    }
}
