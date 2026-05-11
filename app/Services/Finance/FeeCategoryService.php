<?php

namespace App\Services\Finance;

use App\Models\FeeCategory;
use Illuminate\Support\Facades\Auth;

class FeeCategoryService
{
    public function create(array $data): FeeCategory
    {
        $data['created_by'] = Auth::id();

        $data['status'] = $data['status'] ?? true;

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

    public function delete(FeeCategory $feeCategory): bool
    {
        return $feeCategory->delete();
    }
}
