<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Student Marksheet
    </title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{

            background:#d1d5db;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            padding:20px;

            color:#111827;
        }

        .sheet{

            width:1000px;

            margin:auto;

            background:#ffffff;

            border:2px solid #4b5563;

            position:relative;

            overflow:hidden;
        }

        /*
        |--------------------------------------------------------------------------
        | Watermark
        |--------------------------------------------------------------------------
        */

        .watermark{

            position:absolute;

            top:50%;

            left:50%;

            transform:translate(-50%,-50%);

            opacity:0.04;

            z-index:0;
        }

        .watermark img{

            width:500px;
        }

        .content{

            position:relative;

            z-index:2;
        }

        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        .header{

            padding:10px 15px;

            border-bottom:1px solid #9ca3af;
        }

        .header-flex{

            display:flex;

            justify-content:space-between;

            align-items:flex-start;

            gap:10px;
        }

        .logo-box img{

            width:90px;

            height:90px;

            object-fit:contain;
        }

        .school-center{

            flex:1;

            text-align:center;
        }

        .school-name{

            font-size:34px;

            font-weight:900;

            text-transform:uppercase;

            line-height:1.2;
        }

        .school-address{

            margin-top:4px;

            font-size:13px;

            font-weight:600;
        }

        .school-details{

            margin-top:6px;

            display:flex;

            justify-content:center;

            gap:25px;

            font-size:12px;
        }

        .report-title{

            margin-top:12px;

            font-size:22px;

            font-weight:800;

            text-transform:uppercase;
        }

        .right-side{

            text-align:center;
        }

        .qr-box img{

            width:90px;

            height:90px;

            border:1px solid #d1d5db;
        }

        .student-photo img{

            width:90px;

            height:100px;

            margin-top:8px;

            border:1px solid #d1d5db;

            object-fit:cover;
        }

        /*
        |--------------------------------------------------------------------------
        | Section Title
        |--------------------------------------------------------------------------
        */

        .section-title{

            background:#d98b8b;

            padding:8px;

            text-align:center;

            font-size:14px;

            font-weight:800;

            text-transform:uppercase;

            border-top:1px solid #9ca3af;

            border-bottom:1px solid #9ca3af;
        }

        /*
        |--------------------------------------------------------------------------
        | Student Information
        |--------------------------------------------------------------------------
        */

        .student-info{

            padding:12px;
        }

        .info-table{

            width:100%;

            border-collapse:collapse;
        }

        .info-table td{

            padding:6px 8px;

            font-size:13px;
        }

        .label{

            width:160px;

            font-weight:700;

            text-transform:uppercase;
        }

        /*
        |--------------------------------------------------------------------------
        | Marks Table
        |--------------------------------------------------------------------------
        */

        .marks-table{

            width:100%;

            border-collapse:collapse;
        }

        .marks-table th,
        .marks-table td{

            border:1px solid #6b7280;

            padding:8px 6px;

            text-align:center;

            font-size:13px;
        }

        .marks-table th{

            background:#f3f4f6;

            font-weight:800;

            text-transform:uppercase;
        }

        .subject-name{

            text-align:left !important;

            font-weight:700;
        }

        .pass{

            color:#15803d;

            font-weight:800;
        }

        .fail{

            color:#dc2626;

            font-weight:800;
        }

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        .summary{

            display:grid;

            grid-template-columns:
                repeat(5,1fr);
        }

        .summary-box{

            border:1px solid #6b7280;

            border-top:none;

            padding:15px;

            text-align:center;
        }

        .summary-title{

            font-size:12px;

            text-transform:uppercase;

            font-weight:700;

            color:#374151;
        }

        .summary-value{

            margin-top:6px;

            font-size:24px;

            font-weight:800;
        }

        /*
        |--------------------------------------------------------------------------
        | Signatures
        |--------------------------------------------------------------------------
        */

        .signatures{

            display:grid;

            grid-template-columns:
                repeat(4,1fr);

            gap:30px;

            padding:30px 20px;
        }

        .sign-box{

            text-align:center;
        }

        .line{

            margin-top:50px;

            border-top:1px solid #111827;

            padding-top:6px;

            font-size:13px;

            font-weight:700;
        }

        /*
        |--------------------------------------------------------------------------
        | Instructions
        |--------------------------------------------------------------------------
        */

        .instructions{

            border-top:1px solid #6b7280;

            padding:15px;

            font-size:12px;

            line-height:1.8;
        }

        .instructions h3{

            margin-bottom:8px;

            font-size:14px;

            text-transform:uppercase;
        }

        /*
        |--------------------------------------------------------------------------
        | Print
        |--------------------------------------------------------------------------
        */

        .print-btn{

            position:fixed;

            right:25px;

            bottom:25px;

            background:#111827;

            color:white;

            border:none;

            padding:14px 20px;

            border-radius:6px;

            font-size:15px;

            font-weight:700;

            cursor:pointer;
        }

        @media print{

            body{
                background:white;
                padding:0;
            }

            .sheet{
                width:100%;
                border:none;
            }

            .print-btn{
                display:none;
            }
        }

    </style>

</head>

<body>

<div class="sheet">

    @if(!empty($schoolLogo))

        <div class="watermark">

            <img
                src="{{ asset($schoolLogo) }}"
                alt=""
            >

        </div>

    @endif

    <div class="content">

        <!-- HEADER -->

        <!-- HEADER -->

<div class="header">

    <div class="header-flex">

        <!-- LEFT LOGO -->

        <div class="logo-box">

            @if(!empty($schoolLogo))

                <img
                    src="{{ asset($schoolLogo) }}"
                    alt="School Logo"
                >

            @endif

        </div>

        <!-- CENTER -->

        <div class="school-center">

    <div class="school-name">

        {{ $school->name ?? 'Demo School' }}

    </div>

    <div class="school-address">

        {{ $school->address ?? 'School Address Not Available' }}

    </div>

    <div class="school-details">

        <div>

            <strong>Affiliated To:</strong>

            {{
                optional($school->schoolDetailAffiliation)->meta_value
                ??
                'CBSE'
            }}

        </div>

        <div>

            <strong>Phone:</strong>

            {{ $school->phone ?? '-' }}

        </div>

    </div>

    <div class="school-details">

        <div>

            <strong>Email:</strong>

            {{ $school->email ?? '-' }}

        </div>

        <div>

            <strong>Slogan:</strong>

            {{
                optional($school->schoolDetailSlogan)->meta_value
                ??
                'Knowledge Is Power'
            }}

        </div>

    </div>

    <div class="report-title">

        PROGRESS REPORT CARD -
        {{ $academicYear->academic_session ?? date('Y') . '-' . (date('Y') + 1) }}

    </div>

</div>

        <!-- RIGHT -->

        <div class="right-side">

            <div class="qr-box">

                <img
                    src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ url()->current() }}"
                    alt="QR Code"
                >

            </div>

            <div class="student-photo">

                <img
                    src="{{ asset(optional($student->userprofile)->avatar ?? 'uploads/default.png') }}"
                    alt="Student Photo"
                >

            </div>

        </div>

    </div>

</div>

        <!-- STUDENT PROFILE -->

        <div class="section-title">
            Student Profile
        </div>

        <div class="student-info">

            <table class="info-table">

    <tr>

        <td class="label">
            Student Name
        </td>

        <td>

            {{
                optional($student->userprofile)->firstname
            }}

            {{
                optional($student->userprofile)->lastname
            }}

        </td>

        <td class="label">
            Father Name
        </td>

        <td>

            {{
                optional($student->studentParent)->father_name
                ??
                optional($student->userprofile)->father_name
                ??
                optional($student->userprofile)->fathername
                ??
                '-'
            }}

        </td>

    </tr>

    <tr>

        <td class="label">
            Roll Number
        </td>

        <td>

            {{ $student->registration_number ?? '-' }}

        </td>

        <td class="label">
            Admission No
        </td>

        <td>

            {{ $student->id }}

        </td>

    </tr>

    <tr>

        <td class="label">
            Class & Section
        </td>

        <td>

            {{
                optional($exam->standardLink)->StandardSection
                ??
                '-'
            }}

        </td>

        <td class="label">
            Examination
        </td>

        <td>

            {{ $exam->name }}

        </td>

    </tr>

    <tr>

        <td class="label">
            Academic Session
        </td>

        <td>

            {{
                $academicYear->academic_session
                ??
                date('Y') . '-' . (date('Y') + 1)
            }}

        </td>

        <td class="label">
            Rank
        </td>

        <td>

            #{{ $rank }}

        </td>

    </tr>

    <tr>

        <td class="label">
            Date Of Birth
        </td>

        <td>

            {{
                optional($student->userprofile)->dob
                ??
                '-'
            }}

        </td>

        <td class="label">
            Gender
        </td>

        <td>

            {{
                optional($student->userprofile)->gender
                ??
                '-'
            }}

        </td>

    </tr>

</table>

        </div>

        <!-- MARKS -->

        <div class="section-title">
            Academic Performance - Scholastic Area
        </div>

        <table class="marks-table">

            <thead>

            <tr>

                <th>
                    Subject
                </th>

                <th>
                    Max Marks
                </th>

                <th>
                    Pass Marks
                </th>

                <th>
                    Obtained
                </th>

                <th>
                    Grade
                </th>

                <th>
                    Attendance
                </th>

                <th>
                    Result
                </th>

            </tr>

            </thead>

            <tbody>

            @foreach($marks as $mark)

                <tr>

                    <td class="subject-name">

                        {{ optional($mark->examSubject)->subject_name }}

                    </td>

                    <td>

                        {{ optional($mark->examSubject)->max_marks }}

                    </td>

                    <td>

                        {{ optional($mark->examSubject)->pass_marks }}

                    </td>

                    <td>

                        {{ $mark->marks_obtained }}

                    </td>

                    <td>

                        {{ $mark->grade }}

                    </td>

                    <td>

                        {{ ucfirst($mark->attendance_status) }}

                    </td>

                    <td>

                        @if($mark->is_passed)

                            <span class="pass">
                                PASS
                            </span>

                        @else

                            <span class="fail">
                                FAIL
                            </span>

                        @endif

                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

        <!-- SUMMARY -->

        <div class="summary">

            <div class="summary-box">

                <div class="summary-title">
                    Total Marks
                </div>

                <div class="summary-value">

                    {{ $totalMaximum }}

                </div>

            </div>

            <div class="summary-box">

                <div class="summary-title">
                    Obtained Marks
                </div>

                <div class="summary-value">

                    {{ $totalObtained }}

                </div>

            </div>

            <div class="summary-box">

                <div class="summary-title">
                    Percentage
                </div>

                <div class="summary-value">

                    {{ number_format($percentage, 2) }}%

                </div>

            </div>

            <div class="summary-box">

                <div class="summary-title">
                    Final Grade
                </div>

                <div class="summary-value">

                    {{ $finalGrade }}

                </div>

            </div>

            <div class="summary-box">

                <div class="summary-title">
                    Result
                </div>

                <div class="summary-value">

                    {{ strtoupper($finalResult) }}

                </div>

            </div>

        </div>

        <!-- SIGNATURES -->

        <div class="signatures">

            <div class="sign-box">

                <div>
                    {{ now()->format('d-m-Y') }}
                </div>

                <div class="line">
                    Issued Date
                </div>

            </div>

            <div class="sign-box">

                <div class="line">
                    Parent Signature
                </div>

            </div>

            <div class="sign-box">

                <div class="line">
                    Class Teacher Signature
                </div>

            </div>

            <div class="sign-box">

                <div class="line">
                    Principal Signature
                </div>

            </div>

        </div>

        <!-- INSTRUCTIONS -->

        <div class="instructions">

            <h3>
                Instructions
            </h3>

            <div>
                1. Passing marks are based on subject-wise passing criteria.
            </div>

            <div>
                2. Student must pass individually in all subjects.
            </div>

            <div>
                3. This marksheet is ERP generated and valid digitally.
            </div>

            <div>
                4. Verification can be done using the QR code printed above.
            </div>

            <div>
                5. Contact school administration for corrections if any.
            </div>

        </div>

    </div>

</div>

<button
    class="print-btn"
    onclick="window.print()"
>
    Print Marksheet
</button>

</body>
</html>
