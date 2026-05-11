<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\StoreFeeCategoryRequest;
use App\Http\Requests\Finance\UpdateFeeCategoryRequest;
use App\Models\FeeCategory;
use App\Services\Finance\FeeCategoryService;

class FeeCategoryController extends Controller
{
    protected FeeCategoryService $feeCategoryService;

    public function __construct(
        FeeCategoryService $feeCategoryService
    ) {
        $this->feeCategoryService = $feeCategoryService;
    }

    public function index()
    {
        $feeCategories = FeeCategory::query()

            ->currentSchool()
            ->currentAcademicYear()

            ->latest()

            ->paginate(20);

        return view(
            'admin.finance.fee-categories.index',
            compact('feeCategories')
        );
    }

    public function create()
    {
        return view(
            'admin.finance.fee-categories.create'
        );
    }

    public function store(
        StoreFeeCategoryRequest $request
    ) {

        $this->feeCategoryService->create(
            $request->validated()
        );

        return redirect()

            ->route(
                'finance.fee-categories.index'
            )

            ->with(
                'success',
                'Fee category created successfully.'
            );
    }

    public function edit(FeeCategory $feeCategory)
    {
        return view(
            'finance.fee-categories.edit',
            compact('feeCategory')
        );
    }

    public function update(
        UpdateFeeCategoryRequest $request,
        FeeCategory $feeCategory
    ) {

        $this->feeCategoryService->update(
            $feeCategory,
            $request->validated()
        );

        return redirect()

            ->route(
                'finance.fee-categories.index'
            )

            ->with(
                'success',
                'Fee category updated successfully.'
            );
    }

    public function destroy(FeeCategory $feeCategory)
    {
        $this->feeCategoryService->delete(
            $feeCategory
        );

        return redirect()

            ->back()

            ->with(
                'success',
                'Fee category deleted successfully.'
            );
    }
}
