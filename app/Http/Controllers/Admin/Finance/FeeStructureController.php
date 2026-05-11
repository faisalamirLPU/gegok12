<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\StoreFeeStructureRequest;
use App\Models\FeeCategory;
use App\Models\FeeStructure;
use App\Models\Section;
use App\Models\StandardLink;
use App\Services\Finance\FeeStructureService;
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

        $classes = StandardLink::query()
            ->with('standard')
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $academicYear->id)
            ->where('status', true)
            ->get()
            ->unique('standard_id')
            ->values();

        $sections = Section::query()
            ->where('school_id', $schoolId)
            ->get();

        $feeCategories = FeeCategory::query()
            ->currentSchool()
            ->currentAcademicYear()
            ->active()
            ->get();

        $structure = new FeeStructure();

        return view(
            'admin.finance.fee-structures.create',
            compact(
                'classes',
                'sections',
                'feeCategories',
                'structure'
            )
        );
    }

    public function store(StoreFeeStructureRequest $request)
    {
        $this->feeStructureService->create(
            $request->validated()
        );

        return redirect()
            ->route('finance.fee-structures.index')
            ->with('success', 'Fee structure created successfully.');
    }
}
