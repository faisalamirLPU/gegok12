<?php

namespace App\Services\Finance;

use App\Models\FeeCategory;
use App\Services\Audit\AuditTrailService;
use App\Services\Finance\FinanceArchiveService;
use Illuminate\Support\Facades\Auth;

class FeeCategoryService
{
    public function __construct(
        protected FinanceArchiveService $archiveService
    ) {
    }

    public function create(array $data): FeeCategory
    {
        $data['created_by'] = Auth::id();
        $data['status'] = $data['status'] ?? true;
        $data['is_archived'] = false;

        return FeeCategory::create($data);
    }

    public function update(
        FeeCategory $feeCategory,
        array $data
    ): FeeCategory {
        $data['updated_by'] = Auth::id();
        $feeCategory->update($data);
        return $feeCategory->fresh();
    }

    public function delete(FeeCategory $feeCategory): array
    {
        $hasHistoricalUsage = $feeCategory->feeItems()->exists()
            || $feeCategory->studentSpecialFees()->withTrashed()->exists()
            || $feeCategory->structureItems()->exists();

        $feeCategory->fill([
            'status' => false,
            'is_archived' => true,
        ])->save();

        if (! $feeCategory->trashed()) {
            $feeCategory->delete();
        }

        AuditTrailService::log(
            'archive_fee_category',
            $hasHistoricalUsage
                ? sprintf('Archived fee category #%s because of historical financial references.', $feeCategory->id)
                : sprintf('Archived unused fee category #%s.', $feeCategory->id),
            FeeCategory::class,
            $feeCategory->id,
            null,
            $feeCategory->toArray()
        );

        return [
            'message' => $hasHistoricalUsage
                ? 'Category archived because historical financial records exist.'
                : 'Fee category archived successfully.',
        ];
    }

    public function restore(int $id): FeeCategory
    {
        $feeCategory = FeeCategory::withTrashed()->findOrFail($id);
        $feeCategory->restore();
        $feeCategory->fill([
            'status' => true,
            'is_archived' => false,
        ])->save();

        AuditTrailService::log(
            'restore_fee_category',
            sprintf('Restored fee category #%s.', $feeCategory->id),
            FeeCategory::class,
            $feeCategory->id,
            null,
            $feeCategory->toArray()
        );

        return $feeCategory;
    }
}
