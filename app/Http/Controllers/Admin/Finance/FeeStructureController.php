<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\StoreFeeStructureRequest;
use App\Models\FeeCategory;
use App\Models\FeeStructure;
use App\Services\Finance\FeeStructureService;
use App\Models\Standard;
use App\Models\Section;
use App\Helpers\SiteHelper;
use Illuminate\Support\Facades\Auth;

class FeeStructureController extends Controller
{
    protected FeeStructureService $feeStructureService;

    public function __construct(FeeStructureService $feeStructureService)
    {
        $this->feeStructureService = $feeStructureService;
    }

    public function index()
    {
        $structures = FeeStructure::query()
            ->with(['items.feeCategory'])
            ->currentSchool()
            ->currentAcademicYear()
            ->latest()
            ->paginate(20);

        return view(
            'admin.finance.fee-structures.index',
            compact('structures')
        );
    }

    public function create()
    {
        $schoolId = Auth::user()->school_id;

        $academicYear = SiteHelper::getAcademicYear($schoolId);

        $classes = Standard::where([
            ['school_id', $schoolId],
            ['academic_year_id', $academicYear->id]
        ])->get();

        $sections = Section::where([
            ['school_id', $schoolId],
            ['academic_year_id', $academicYear->id]
        ])->get();

        $feeCategories = FeeCategory::query()
            ->currentSchool()
            ->currentAcademicYear()
            ->active()
            ->get();

        return view(
            'admin.finance.fee-structures.create',
            compact(
                'classes',
                'sections',
                'feeCategories'
            )
        );
    }

    public function store(StoreFeeStructureRequest $request)
    {
        $this->feeStructureService->create(
            $request->validated()
        );

        return redirect()
            ->route('admin.finance.fee-structures.index')
            ->with('success', 'Fee structure created successfully.');
    }
}
