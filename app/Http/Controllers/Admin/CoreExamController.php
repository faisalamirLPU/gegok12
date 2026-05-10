<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\AcademicYear;
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
    | Create Page
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $classes = $this->classes();

        $subjectsByClass = [];

        foreach ($classes as $class) {
            $subjectsByClass[$class->id] = Subject::where(
                'school_id',
                Auth::user()->school_id
            )
                ->where('standard_id', $class->standard_id)
                ->where('section_id', $class->section_id)
                ->orderBy('name', 'ASC')
                ->get();
        }

        $examNames = CoreExam::where(
            'school_id',
            Auth::user()->school_id
        )
            ->distinct()
            ->pluck('name');

        return view('admin.core.exams.create', [
            'classes' => $classes,
            'subjectsByClass' => $subjectsByClass,
            'examNames' => $examNames,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Store Exam
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $request->validate([

            'name'                  =>
            'required_without:name_custom|string|max:255',

            'name_custom'           =>
            'nullable|string|max:255',

            'standard_link_id'      =>
            'required|integer|exists:standards_link,id',

            'exam_date'             =>
            'nullable|date',

            'status'                =>
            'nullable|in:draft,scheduled',

            /*
            |--------------------------------------------------------------------------
            | Subjects
            |--------------------------------------------------------------------------
            */

            'subjects'              =>
            'required|array|min:1',

            'subjects.*.name'       =>
            'required|string|max:255',

            'subjects.*.max_marks'  =>
            'required|numeric|min:1',

            'subjects.*.pass_marks' =>
            'required|numeric|min:0',

            'subjects.*.exam_date'  =>
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

                'school_id'        =>
                Auth::user()->school_id,

                'academic_year_id' =>
                $standardLink->academic_year_id,

                'standard_link_id' =>
                $standardLink->id,

                'name'             =>
                trim($data['name_custom'] ?? $data['name']),

                'exam_date'        =>
                $data['exam_date'] ?? null,

                'status'           =>
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

                    'subject_id'   =>
                    optional($subject)->id,

                    'subject_name' =>
                    $subjectName,

                    'max_marks'    =>
                    $subjectRow['max_marks'],

                    'pass_marks'   =>
                    $subjectRow['pass_marks'],

                    'exam_date'    =>
                    $subjectRow['exam_date'] ?? null,
                ]);
            }

            DB::commit();

            return redirect(
                '/admin/exams/' . $exam->id
            )->with(
                'successmessage',
                'Exam created successfully.'
            );

        } catch (\Exception $e) {

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

        /*
    |--------------------------------------------------------------------------
    | Load Relations
    |--------------------------------------------------------------------------
    */

        $exam->load([

            'subjects',

            'standardLink.standard',

            'standardLink.section',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Students
    |--------------------------------------------------------------------------
    */

        $students = User::query()

            ->select('users.*')

            ->join(
                'student_academics',
                'student_academics.user_id',
                '=',
                'users.id'
            )

            ->leftJoin(
                'userprofiles',
                'userprofiles.user_id',
                '=',
                'users.id'
            )

            ->where(
                'student_academics.school_id',
                Auth::user()->school_id
            )

            ->where(
                'student_academics.standardLink_id',
                $exam->standard_link_id
            )

            ->when(
                request()->filled('search'),
                function ($query) {

                    $search = request('search');

                    $query->where(function ($q) use ($search) {

                        /*
                |--------------------------------------------------------------------------
                | Registration Number
                |--------------------------------------------------------------------------
                */

                        $q->where(
                            'users.registration_number',
                            'like',
                            "%{$search}%"
                        )

                        /*
                |--------------------------------------------------------------------------
                | Full Name
                |--------------------------------------------------------------------------
                */

                            ->orWhereRaw(
                                "CONCAT(
                        COALESCE(userprofiles.firstname, ''),
                        ' ',
                        COALESCE(userprofiles.lastname, '')
                    ) LIKE ?",
                                ["%{$search}%"]
                            )

                        /*
                |--------------------------------------------------------------------------
                | First Name
                |--------------------------------------------------------------------------
                */

                            ->orWhere(
                                'userprofiles.firstname',
                                'like',
                                "%{$search}%"
                            )

                        /*
                |--------------------------------------------------------------------------
                | Last Name
                |--------------------------------------------------------------------------
                */

                            ->orWhere(
                                'userprofiles.lastname',
                                'like',
                                "%{$search}%"
                            )

                        /*
                |--------------------------------------------------------------------------
                | Username / Name
                |--------------------------------------------------------------------------
                */

                            ->orWhere(
                                'users.name',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )

            ->with('userprofile')

            ->orderBy(
                'userprofiles.firstname'
            )

            ->paginate(10)

            ->withQueryString();

        /*
    |--------------------------------------------------------------------------
    | Subject Statistics
    |--------------------------------------------------------------------------
    */

        foreach ($exam->subjects as $subject) {

            $subject->marks_entered =
            CoreMark::where(
                'core_exam_subject_id',
                $subject->id
            )->count();

            $subject->pass_count =
            CoreMark::where(
                'core_exam_subject_id',
                $subject->id
            )
                ->where(
                    'is_passed',
                    true
                )
                ->count();

            $subject->fail_count =
            CoreMark::where(
                'core_exam_subject_id',
                $subject->id
            )
                ->where(
                    'is_passed',
                    false
                )
                ->whereNotNull('marks_obtained')
                ->count();
        }

        /*
    |--------------------------------------------------------------------------
    | Student Result Status
    |--------------------------------------------------------------------------
    */

        foreach ($students as $student) {

            $student->marks_count =
            CoreMark::where(
                'core_exam_id',
                $exam->id
            )
                ->where(
                    'student_id',
                    $student->id
                )
                ->count();

            $student->total_subjects =
            $exam->subjects->count();

            $student->result_completed =
            $student->marks_count >=
            $student->total_subjects;
        }

        return view(
            'admin.core.exams.show',
            [
                'exam'     => $exam,
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
                'exam'     => $exam,
                'subject'  => $subject,

                'students' =>
                $this->students($exam),

                'marks'    =>
                CoreMark::where(
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
            ->map(fn($id) => (int) $id)
            ->all();

        $data = $request->validate([

            'marks'                     =>
            'required|array',

            'marks.*.student_id'        =>
            'required|integer|exists:users,id',

            'marks.*.marks_obtained'    =>
            'nullable|numeric|min:0|max:' .
            $subject->max_marks,

            'marks.*.attendance_status' =>
            'required|in:present,absent,medical',

            'marks.*.remarks'           =>
            'nullable|string|max:1000',
        ]);

        foreach ($data['marks'] as $row) {

            if (
                ! in_array(
                    (int) $row['student_id'],
                    $studentIds,
                    true
                )
            ) {
                continue;
            }

            $attendanceStatus =
                $row['attendance_status'];

            $marks =
            $attendanceStatus === 'present'
                ? ($row['marks_obtained'] ?? 0)
                : null;

            $isPassed = false;

            if (
                $attendanceStatus === 'present' &&
                $marks !== null
            ) {
                $isPassed =
                $marks >= $subject->pass_marks;
            }

            CoreMark::updateOrCreate(

                [
                    'core_exam_subject_id' =>
                    $subject->id,

                    'student_id'           =>
                    $row['student_id'],
                ],

                [
                    'core_exam_id'      =>
                    $exam->id,

                    'marks_obtained'    =>
                    $marks,

                    'attendance_status' =>
                    $attendanceStatus,

                    'grade'             =>
                    $marks === null
                        ? null
                        : $this->grade(
                        (float) $marks,
                        (float) $subject->max_marks
                    ),

                    'is_passed'         =>
                    $isPassed,

                    'remarks'           =>
                    $row['remarks'] ?? null,

                    'checked_by'        =>
                    Auth::id(),

                    'checked_at'        =>
                    now(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Auto Update Status
        |--------------------------------------------------------------------------
        */

        if ($exam->status === 'draft') {

            $exam->update([
                'status' => 'ongoing',
            ]);
        }

        return redirect(
            '/admin/exams/' . $exam->id
        )->with(
            'successmessage',
            'Marks saved successfully.'
        );
    }

    public function marksheet(
    CoreExam $exam,
    User $student
) {

    /*
    |--------------------------------------------------------------------------
    | Security Check
    |--------------------------------------------------------------------------
    */

    $this->authorizeSchool($exam);

    /*
    |--------------------------------------------------------------------------
    | Validate Student Belongs To This Class
    |--------------------------------------------------------------------------
    */

    $studentExists = StudentAcademic::where(
            'school_id',
            Auth::user()->school_id
        )
        ->where(
            'standardLink_id',
            $exam->standard_link_id
        )
        ->where(
            'user_id',
            $student->id
        )
        ->exists();

    abort_unless($studentExists, 404);

    /*
    |--------------------------------------------------------------------------
    | Load Exam Subjects
    |--------------------------------------------------------------------------
    */

    $exam->load([
        'subjects',
        'standardLink.standard',
        'standardLink.section',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Student Marks
    |--------------------------------------------------------------------------
    */

    $marks = CoreMark::where(
            'core_exam_id',
            $exam->id
        )
        ->where(
            'student_id',
            $student->id
        )
        ->with([
            'examSubject'
        ])
        ->get();

    /*
    |--------------------------------------------------------------------------
    | School Details
    |--------------------------------------------------------------------------
    */

    $school = Auth::user()
        ->school
        ->load([
            'schoolDetailLogo',
            'schoolDetailSlogan',
            'schoolDetailAffiliation',
        ]);

    /*
    |--------------------------------------------------------------------------
    | School Logo
    |--------------------------------------------------------------------------
    */

    $schoolLogo =
        optional(
            $school->schoolDetailLogo
        )->LogoPath;

    /*
    |--------------------------------------------------------------------------
    | Totals
    |--------------------------------------------------------------------------
    */

    $totalMaximum = $marks
        ->sum(function ($mark) {

            return optional(
                $mark->examSubject
            )->max_marks ?? 0;
        });

    $totalObtained = $marks
        ->sum('marks_obtained');

    /*
    |--------------------------------------------------------------------------
    | Percentage
    |--------------------------------------------------------------------------
    */

    $percentage = $totalMaximum > 0
        ? round(
            ($totalObtained / $totalMaximum) * 100
        )
        : 0;

    /*
    |--------------------------------------------------------------------------
    | Final Grade
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
    | Final Result
    |--------------------------------------------------------------------------
    */

    $finalResult = $marks
        ->where('is_passed', false)
        ->count() > 0
            ? 'FAIL'
            : 'PASS';

    /*
    |--------------------------------------------------------------------------
    | Ranking System
    |--------------------------------------------------------------------------
    */

    $students = $this->students($exam);

    $rankings = [];

    foreach ($students as $s) {

        $studentMarks = CoreMark::where(
                'core_exam_id',
                $exam->id
            )
            ->where(
                'student_id',
                $s->id
            )
            ->get();

        $obtained = $studentMarks
            ->sum('marks_obtained');

        $rankings[] = [

            'student_id' => $s->id,

            'total' => $obtained,
        ];
    }

    $rankings = collect($rankings)
        ->sortByDesc('total')
        ->values();

    $rank = 1;

    foreach ($rankings as $index => $row) {

        if (
            (int) $row['student_id'] ===
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

    $academicYear = null;

if (!empty($exam->academic_year_id)) {

    $academicYear = \App\Models\AcademicYear::find(
        $exam->academic_year_id
    );
}

    /*
    |--------------------------------------------------------------------------
    | Return View
    |--------------------------------------------------------------------------
    */

    return view(
        'admin.core.exams.marksheet',
        [

            'exam' => $exam,

            'student' => $student,

            'school' => $school,

            'schoolLogo' => $schoolLogo,

            'marks' => $marks,

            'academicYear' => $academicYear,

            'totalMaximum' => $totalMaximum,

            'totalObtained' => $totalObtained,

            'percentage' => $percentage,

            'finalGrade' => $finalGrade,

            'finalResult' => $finalResult,

            'rank' => $rank,
        ]
    );
}

    /*
    |--------------------------------------------------------------------------
    | Students
    |--------------------------------------------------------------------------
    */

    private function students(CoreExam $exam)
    {
        return User::query()

            ->select('users.*')

            ->join(
                'student_academics',
                'student_academics.user_id',
                '=',
                'users.id'
            )

            ->where(
                'student_academics.school_id',
                Auth::user()->school_id
            )

            ->where(
                'student_academics.standardLink_id',
                $exam->standard_link_id
            )

            ->with('userprofile')

            ->orderBy('users.name')

            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Classes
    |--------------------------------------------------------------------------
    */

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
            ->where('status', 1)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Authorize School
    |--------------------------------------------------------------------------
    */

    private function authorizeSchool(
        CoreExam $exam
    ): void {

        abort_unless(
            (int) $exam->school_id ===
            (int) Auth::user()->school_id,
            404
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Authorize Subject Access
    |--------------------------------------------------------------------------
    */

    private function authorizeExamSubjectAccess(
        CoreExam $exam,
        CoreExamSubject $subject
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Same School
        |--------------------------------------------------------------------------
        */

        if (
            (int) $exam->school_id !==
            (int) Auth::user()->school_id
        ) {
            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | Subject Belongs To Exam
        |--------------------------------------------------------------------------
        */

        if (
            (int) $subject->core_exam_id !==
            (int) $exam->id
        ) {
            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | Admin Bypass
        |--------------------------------------------------------------------------
        */

        if (
            (int) Auth::user()->usergroup_id === 3
        ) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Teacher Validation
        |--------------------------------------------------------------------------
        */

        $allowed = Teacherlink::where(
            'school_id',
            Auth::user()->school_id
        )
            ->where(
                'teacher_id',
                Auth::id()
            )
            ->where(
                'standardLink_id',
                $exam->standard_link_id
            )
            ->where(
                'subject_id',
                $subject->subject_id
            )
            ->exists();

        if (! $allowed) {
            abort(404);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Grade Generator
    |--------------------------------------------------------------------------
    */

    private function grade(
        float $marks,
        float $maxMarks
    ): string {

        $percentage =
            ($marks / $maxMarks) * 100;

        return match (true) {

            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B+',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C',
            $percentage >= 35 => 'D',

            default           => 'F',
        };
    }
}
