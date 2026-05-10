<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\AcademicYear;
use App\Models\CoreExam;
use App\Models\CoreExamSubject;
use App\Models\CoreMark;
use App\Models\StandardLink;
use App\Models\StudentAcademic;
use App\Models\Subject;
use App\Models\Teacherlink;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CoreExamController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Exam List
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $exams = CoreExam::with([
                'standardLink.standard',
                'standardLink.section',
                'subjects',
            ])
            ->where(
                'school_id',
                Auth::user()->school_id
            )
            ->latest()
            ->paginate(15);

        return view(
            'admin.core.exams.index',
            compact('exams')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create Exam Page
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view(
            'admin.core.exams.create',
            [
                'classes' => $this->classes(),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store Exam
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $request->validate([

            'name' =>
                'required|string|max:255',

            'standard_link_id' =>
                'required|integer|exists:standards_link,id',

            'exam_date' =>
                'nullable|date',

            'status' =>
                'nullable|in:draft,scheduled',

            /*
            |--------------------------------------------------------------------------
            | Subjects
            |--------------------------------------------------------------------------
            */

            'subjects' =>
                'required|array|min:1',

            'subjects.*.name' =>
                'required|string|max:255',

            'subjects.*.max_marks' =>
                'required|numeric|min:1',

            'subjects.*.pass_marks' =>
                'required|numeric|min:0',

            'subjects.*.exam_date' =>
                'nullable|date',
        ]);

        $standardLink = StandardLink::where(
                'school_id',
                Auth::user()->school_id
            )
            ->findOrFail(
                $data['standard_link_id']
            );

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Create Exam
            |--------------------------------------------------------------------------
            */

            $exam = CoreExam::create([

                'school_id' =>
                    Auth::user()->school_id,

                'academic_year_id' =>
                    $standardLink->academic_year_id,

                'standard_link_id' =>
                    $standardLink->id,

                'name' =>
                    $data['name'],

                'exam_date' =>
                    $data['exam_date'] ?? null,

                'status' =>
                    $data['status'] ?? 'draft',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Subjects
            |--------------------------------------------------------------------------
            */

            foreach ($data['subjects'] as $subjectRow) {

                $subjectName =
                    trim($subjectRow['name']);

                if (empty($subjectName)) {
                    continue;
                }

                $subject = Subject::where(
                        'school_id',
                        Auth::user()->school_id
                    )
                    ->where(
                        'standard_id',
                        $standardLink->standard_id
                    )
                    ->where(
                        'section_id',
                        $standardLink->section_id
                    )
                    ->where(
                        'name',
                        $subjectName
                    )
                    ->first();

                CoreExamSubject::create([

                    'core_exam_id' =>
                        $exam->id,

                    'subject_id' =>
                        $subject?->id,

                    'subject_name' =>
                        $subjectName,

                    'max_marks' =>
                        $subjectRow['max_marks'],

                    'pass_marks' =>
                        $subjectRow['pass_marks'],

                    'exam_date' =>
                        $subjectRow['exam_date'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route(
                    'admin.exams.show',
                    $exam->id
                )
                ->with(
                    'successmessage',
                    'Exam created successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'errormessage',
                    $e->getMessage()
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Show Exam
    |--------------------------------------------------------------------------
    */

    public function show(CoreExam $exam)
    {
        $this->authorizeSchool($exam);

        $search = request('search');

        $students = $this->students($exam);

        if ($search) {

            $students = $students->filter(function ($student) use ($search) {

                $fullName =
                    strtolower(
                        optional($student->userprofile)->firstname .
                        ' ' .
                        optional($student->userprofile)->lastname
                    );

                return
                    str_contains(
                        strtolower($student->registration_number),
                        strtolower($search)
                    )
                    ||
                    str_contains(
                        $fullName,
                        strtolower($search)
                    );
            });
        }

        $students = collect($students)
            ->map(function ($student) use ($exam) {

                $subjectCount =
                    $exam->subjects->count();

                $enteredCount =
                    CoreMark::where(
                        'core_exam_id',
                        $exam->id
                    )
                    ->where(
                        'student_id',
                        $student->id
                    )
                    ->count();

                $student->result_completed =
                    $subjectCount > 0
                    &&
                    $enteredCount >= $subjectCount;

                return $student;
            });

        return view(
            'admin.core.exams.show',
            [
                'exam' => $exam->load([
                    'subjects',
                    'standardLink.standard',
                    'standardLink.section',
                ]),

                'students' => $students,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Marks Entry Page
    |--------------------------------------------------------------------------
    */

    public function marks(
        CoreExam $exam,
        CoreExamSubject $subject
    ) {

        $this->authorizeExamSubjectAccess(
            $exam,
            $subject
        );

        return view(
            'admin.core.exams.marks',
            [
                'exam' => $exam,

                'subject' => $subject,

                'students' => $this->students($exam),

                'marks' => CoreMark::where(
                        'core_exam_subject_id',
                        $subject->id
                    )
                    ->get()
                    ->keyBy('student_id'),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Save Marks
    |--------------------------------------------------------------------------
    */

    public function saveMarks(
        Request $request,
        CoreExam $exam,
        CoreExamSubject $subject
    ) {

        $this->authorizeExamSubjectAccess(
            $exam,
            $subject
        );

        $studentIds = $this->students($exam)
            ->pluck('id')
            ->all();

        $data = $request->validate([

            'marks' =>
                'required|array',

            'marks.*.student_id' =>
                'required|integer|exists:users,id',

            'marks.*.marks_obtained' =>
                'nullable|numeric|min:0|max:' .
                $subject->max_marks,

            'marks.*.remarks' =>
                'nullable|string|max:1000',
        ]);

        foreach ($data['marks'] as $row) {

            abort_unless(
                in_array(
                    $row['student_id'],
                    $studentIds,
                    true
                ),
                404
            );

            $marks =
                $row['marks_obtained'] ?? 0;

            $isPassed =
                $marks >= $subject->pass_marks;

            CoreMark::updateOrCreate(

                [
                    'core_exam_subject_id' =>
                        $subject->id,

                    'student_id' =>
                        $row['student_id'],
                ],

                [
                    'core_exam_id' =>
                        $exam->id,

                    'marks_obtained' =>
                        $marks,

                    'grade' =>
                        $this->grade(
                            (float) $marks,
                            (float) $subject->max_marks
                        ),

                    'is_passed' =>
                        $isPassed,

                    'remarks' =>
                        $row['remarks'] ?? null,
                ]
            );
        }

        if ($exam->status === 'draft') {

            $exam->update([
                'status' => 'ongoing'
            ]);
        }

        return redirect()
            ->route(
                'admin.exams.show',
                $exam->id
            )
            ->with(
                'successmessage',
                'Marks saved successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Marksheet
    |--------------------------------------------------------------------------
    */

    public function marksheet(
        CoreExam $exam,
        User $student
    ) {

        $this->authorizeSchool($exam);

        $marks = CoreMark::where(
                'core_exam_id',
                $exam->id
            )
            ->where(
                'student_id',
                $student->id
            )
            ->with('examSubject')
            ->get();

        abort_if(
            $marks->count() <= 0,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | School
        |--------------------------------------------------------------------------
        */

        $school = Auth::user()
            ->school
            ->load([
                'schoolDetailLogo',
                'schoolDetailSlogan',
                'schoolDetailAffiliation',
            ]);

        $schoolLogo =
            optional(
                $school->schoolDetailLogo
            )->LogoPath;

        /*
        |--------------------------------------------------------------------------
        | Totals
        |--------------------------------------------------------------------------
        */

        $totalMaximum =
            $marks->sum(function ($mark) {

                return optional(
                    $mark->examSubject
                )->max_marks ?? 0;
            });

        $totalObtained =
            $marks->sum('marks_obtained');

        $percentage =
            $totalMaximum > 0
                ? round(
                    ($totalObtained / $totalMaximum) * 100,
                    2
                )
                : 0;

        /*
        |--------------------------------------------------------------------------
        | Grade
        |--------------------------------------------------------------------------
        */

        $finalGrade = match (true) {

            $percentage >= 90 => 'A+',

            $percentage >= 75 => 'A',

            $percentage >= 60 => 'B',

            $percentage >= 45 => 'C',

            $percentage >= 35 => 'D',

            default => 'F',
        };

        /*
        |--------------------------------------------------------------------------
        | Result
        |--------------------------------------------------------------------------
        */

        $finalResult =
            $marks->where(
                'is_passed',
                false
            )->count() > 0
                ? 'FAIL'
                : 'PASS';

        /*
        |--------------------------------------------------------------------------
        | Ranking
        |--------------------------------------------------------------------------
        */

        $students =
            $this->students($exam);

        $rankings = [];

        foreach ($students as $s) {

            $obtained =
                CoreMark::where(
                        'core_exam_id',
                        $exam->id
                    )
                    ->where(
                        'student_id',
                        $s->id
                    )
                    ->sum('marks_obtained');

            $rankings[] = [

                'student_id' =>
                    $s->id,

                'total' =>
                    $obtained,
            ];
        }

        $rankings = collect($rankings)
            ->sortByDesc('total')
            ->values();

        $rank = 1;

        foreach ($rankings as $index => $row) {

            if (
                (int) $row['student_id']
                ===
                (int) $student->id
            ) {

                $rank = $index + 1;

                break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Academic Year
        |--------------------------------------------------------------------------
        */

        $academicYear =
            AcademicYear::find(
                $exam->academic_year_id
            );

        return view(
            'admin.core.exams.marksheet',
            compact(
                'exam',
                'student',
                'marks',
                'school',
                'schoolLogo',
                'academicYear',
                'totalMaximum',
                'totalObtained',
                'percentage',
                'finalGrade',
                'finalResult',
                'rank'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function grade(
        float $marks,
        float $max
    ): string {

        $percentage =
            ($marks / $max) * 100;

        return match (true) {

            $percentage >= 90 => 'A+',

            $percentage >= 75 => 'A',

            $percentage >= 60 => 'B',

            $percentage >= 45 => 'C',

            $percentage >= 35 => 'D',

            default => 'F',
        };
    }

    private function authorizeSchool(
        CoreExam $exam
    ): void {

        abort_unless(
            $exam->school_id ===
            Auth::user()->school_id,
            404
        );
    }

    private function authorizeExamSubjectAccess(
        CoreExam $exam,
        CoreExamSubject $subject
    ): void {

        abort_unless(
            $exam->id ===
            $subject->core_exam_id,
            404
        );

        $this->authorizeSchool($exam);
    }

    private function classes()
    {
        return StandardLink::with([
                'standard',
                'section',
            ])
            ->where(
                'school_id',
                Auth::user()->school_id
            )
            ->get();
    }

    private function students(
        CoreExam $exam
    ) {

        return User::whereHas(
                'studentAcademic',
                function ($query) use ($exam) {

                    $query->where(
                        'standardLink_id',
                        $exam->standard_link_id
                    );
                }
            )
            ->with('userprofile')
            ->get();
    }
}
