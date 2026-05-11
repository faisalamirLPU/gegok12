<?php

namespace App\Enums\Finance;

class InvoiceStatusEnum
{
    public const PENDING = 'pending';

    public const PARTIAL = 'partial';

    public const PAID = 'paid';

    public const OVERDUE = 'overdue';

    public const CANCELLED = 'cancelled';

    public const REFUNDED = 'refunded';

    public static function values(): array
    {
        return [
            self::PENDING,
            self::PARTIAL,
            self::PAID,
            self::OVERDUE,
            self::CANCELLED,
            self::REFUNDED,
        ];
    }
}
