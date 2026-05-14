<?php
namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\StoreFeeCategoryRequest;
use App\Http\Requests\Finance\UpdateFeeCategoryRequest;
use App\Models\FeeCategory;
use App\Services\Finance\FeeCategoryService;
use Illuminate\Http\Request;

class FeeCategoryController extends Controller
{
    protected FeeCategoryService $feeCategoryService;

    public function __construct(
        FeeCategoryService $feeCategoryService
    ) {
        $this->feeCategoryService = $feeCategoryService;
    }

    public function index(Request $request)
    {
        $feeCategories = FeeCategory::query()
            ->currentSchool()
            ->currentAcademicYear();

        if ($request->status === 'archived') {
            $feeCategories->onlyTrashed();
        } elseif ($request->status === 'inactive') {
            $feeCategories->where('status', false);
        }

        $feeCategories = $feeCategories->latest()->paginate(20)->withQueryString();

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
                'finance.fee-management.categories'
            )

            ->with(
                'success',
                'Fee category created successfully.'
            );
    }

    public function edit(FeeCategory $feeCategory)
    {
        return view(
            'admin.finance.fee-categories.edit',
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
                'finance.fee-management.categories'
            )

            ->with(
                'success',
                'Fee category updated successfully.'
            );
    }

    public function destroy(FeeCategory $feeCategory)
    {
        $result = $this->feeCategoryService->delete(
            $feeCategory
        );

        return redirect()
            ->back()
            ->with('success', $result['message']);
    }

    public function restore($id)
    {
        $this->feeCategoryService->restore($id);

        return redirect()
            ->back()
            ->with('success', 'Fee category restored successfully.');
    }
}
