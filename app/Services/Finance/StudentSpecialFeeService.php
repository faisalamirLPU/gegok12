<?php

namespace App\Services\Finance;

use App\Models\FeeItem;
use App\Models\StudentSpecialFee;
use App\Services\Audit\AuditTrailService;
use Illuminate\Support\Facades\Auth;

class StudentSpecialFeeService
{
    public function delete(StudentSpecialFee $specialFee): array
    {
        $isBilled = $specialFee->status === 2 || FeeItem::query()
            ->where('source_type', 'special')
            ->where('source_id', $specialFee->id)
            ->exists();

        if ($isBilled) {
            $oldValues = $specialFee->toArray();
            $specialFee->update(['status' => 3]);

            AuditTrailService::log(
                'cancel_special_fee',
                sprintf('Cancelled special fee #%s because it has been billed.', $specialFee->id),
                StudentSpecialFee::class,
                $specialFee->id,
                $oldValues,
                $specialFee->toArray()
            );

            return ['status' => 'cancelled', 'message' => 'Special fee has already been billed and was cancelled instead of deleted.'];
        }

        $specialFee->fill([
            'status' => 0,
            'is_archived' => true,
        ])->save();

        $specialFee->delete();

        AuditTrailService::log(
            'archive_special_fee',
            sprintf('Archived unbilled special fee #%s.', $specialFee->id),
            StudentSpecialFee::class,
            $specialFee->id,
            null,
            $specialFee->toArray()
        );

        return ['status' => 'archived', 'message' => 'Special fee archived successfully.'];
    }

    public function restore(StudentSpecialFee $specialFee): StudentSpecialFee
    {
        $oldValues = $specialFee->getOriginal();
        $specialFee->restore();
        $specialFee->fill([
            'status' => 1,
            'is_archived' => false,
        ])->save();

        AuditTrailService::log(
            'restore_special_fee',
            sprintf('Restored special fee #%s.', $specialFee->id),
            StudentSpecialFee::class,
            $specialFee->id,
            $oldValues,
            $specialFee->toArray()
        );

        return $specialFee;
    }
}
