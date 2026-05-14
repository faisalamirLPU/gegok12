<?php

namespace App\Traits\Finance;

trait ImmutableAccountingRecord
{
    public static function bootImmutableAccountingRecord()
    {
        static::deleting(function ($model) {

            throw new \Exception(
                'Accounting records cannot be deleted. Use reverse or void operations instead.'
            );
        });
    }

    public function delete()
    {
        throw new \Exception(
            'Accounting records cannot be deleted. Use reverse or void operations instead.'
        );
    }
}
