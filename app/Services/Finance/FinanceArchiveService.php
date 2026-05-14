<?php

namespace App\Services\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Services\Audit\AuditTrailService;

class FinanceArchiveService
{
    public function archive(Model $record, string $reason = null): Model
    {
        $original = $record->getOriginal();

        if (method_exists($record, 'archive')) {
            $record->archive();
        } else {
            $record->fill([
                'status' => false,
                'is_archived' => true,
            ])->save();

            if (method_exists($record, 'delete')) {
                $record->delete();
            }
        }

        AuditTrailService::log(
            'archive',
            $reason ?? sprintf('Archived %s #%s', get_class($record), $record->id),
            get_class($record),
            $record->id,
            $original,
            $record->getAttributes()
        );

        return $record;
    }

    public function restore(Model $record, string $reason = null): Model
    {
        $original = $record->getOriginal();

        if (method_exists($record, 'restore')) {
            $record->restore();
        }

        $record->fill([
            'status' => true,
            'is_archived' => false,
        ])->save();

        AuditTrailService::log(
            'restore',
            $reason ?? sprintf('Restored %s #%s', get_class($record), $record->id),
            get_class($record),
            $record->id,
            $original,
            $record->getAttributes()
        );

        return $record;
    }
}
