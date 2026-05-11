<?php

namespace App\Enums\Finance;

class ConcessionTypeEnum
{
    public const FIXED = 'fixed';

    public const PERCENTAGE = 'percentage';

    public const SCHOLARSHIP = 'scholarship';

    public const SIBLING = 'sibling';

    public const STAFF = 'staff';

    public static function values(): array
    {
        return [
            self::FIXED,
            self::PERCENTAGE,
            self::SCHOLARSHIP,
            self::SIBLING,
            self::STAFF,
        ];
    }
}
