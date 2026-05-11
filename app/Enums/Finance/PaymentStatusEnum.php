<?php

namespace App\Enums\Finance;

class PaymentStatusEnum
{
    public const PENDING = 'pending';

    public const SUCCESS = 'success';

    public const FAILED = 'failed';

    public const CANCELLED = 'cancelled';

    public const REFUNDED = 'refunded';

    public static function values(): array
    {
        return [
            self::PENDING,
            self::SUCCESS,
            self::FAILED,
            self::CANCELLED,
            self::REFUNDED,
        ];
    }
}
