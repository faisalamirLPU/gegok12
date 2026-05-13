<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\StoreFeeStructureRequest;
use App\Models\FeeCategory;
use App\Models\FeeStructure;
use App\Models\Section;
use App\Models\Standard;
use App\Models\StandardLink;
use App\Services\Finance\FeeStructureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeStructureController extends Controller
{
    protected FeeStructureService $feeStructureService;

    public function __construct(
        FeeStructureService $feeStructureService
    ) {
        $this->feeStructureService = $feeStructureService;
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $structures = FeeStructure::query()

            ->with([
                'standard',
                'section',
                'items.feeCategory'
            ])

            ->currentSchool()

            ->currentAcademicYear()

            ->latest()

            ->paginate(20);

        return view(
            'admin.finance.fee-structures.index',
            compact('structures')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $schoolId = Auth::user()->school_id;

        $academicYear = SiteHelper::getAcademicYear(
            $schoolId
        );

        /*
        |--------------------------------------------------------------------------
        | Classes
        |--------------------------------------------------------------------------
        */

        $classes = Standard::query()

            ->where('school_id', $schoolId)

            ->where('status', 1)

            ->orderBy('name')

            ->get();

        /*
        |--------------------------------------------------------------------------
        | Sections
        |--------------------------------------------------------------------------
        */

        $sections = Section::query()

            ->where('school_id', $schoolId)

            ->where('status', 1)

            ->orderBy('name')

            ->get();

        /*
        |--------------------------------------------------------------------------
        | Fee Categories
        |--------------------------------------------------------------------------
        */

        $feeCategories = FeeCategory::query()

            ->currentSchool()

            ->currentAcademicYear()

            ->active()

            ->orderBy('name')

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

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(
        StoreFeeStructureRequest $request
    ) {
        $this->feeStructureService->create(
            $request->validated()
        );

        return redirect()

            ->route('finance.fee-management.structures')

            ->with(
                'successmessage',
                'Fee structure created successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $schoolId = Auth::user()->school_id;

        $structure = FeeStructure::query()

            ->with([
                'items.feeCategory'
            ])

            ->where('school_id', $schoolId)

            ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Classes
        |--------------------------------------------------------------------------
        */

        $classes = Standard::query()

            ->where('school_id', $schoolId)

            ->where('status', 1)

            ->orderBy('name')

            ->get();

        /*
        |--------------------------------------------------------------------------
        | Sections
        |--------------------------------------------------------------------------
        */

        $sections = Section::query()

            ->where('school_id', $schoolId)

            ->where('status', 1)

            ->orderBy('name')

            ->get();

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        $feeCategories = FeeCategory::query()

            ->currentSchool()

            ->currentAcademicYear()

            ->active()

            ->orderBy('name')

            ->get();

        return view(
            'admin.finance.fee-structures.edit',
            compact(
                'structure',
                'classes',
                'sections',
                'feeCategories'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        StoreFeeStructureRequest $request,
        $id
    ) {
        $structure = FeeStructure::findOrFail($id);

        $structure->update([

            'class_id' => $request->class_id,

            'section_id' => $request->section_id,

            'title' => $request->title,

            'description' => $request->description,

            'installment_type' => $request->installment_type,

            'due_type' => $request->due_type,

            'due_day' => $request->due_day,

            'status' => $request->status ?? 1,

            'updated_by' => Auth::id(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Recreate Items
        |--------------------------------------------------------------------------
        */

        $structure->items()->delete();

        if ($request->has('items')) {

            foreach ($request->items as $item) {

                if (
                    !empty($item['fee_category_id']) &&
                    !empty($item['amount'])
                ) {

                    $structure->items()->create([

                        'fee_category_id' =>
                            $item['fee_category_id'],

                        'amount' =>
                            $item['amount'],
                    ]);
                }
            }
        }

        return redirect()

            ->route('finance.fee-management.structures')

            ->with(
                'successmessage',
                'Fee structure updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $structure = FeeStructure::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Delete Items First
        |--------------------------------------------------------------------------
        */

        $structure->items()->delete();

        $structure->delete();

        return redirect()

            ->route('finance.fee-management.structures')

            ->with(
                'successmessage',
                'Fee structure deleted successfully.'
            );
    }
}