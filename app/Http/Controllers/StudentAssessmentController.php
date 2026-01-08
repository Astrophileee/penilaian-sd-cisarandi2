<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Semester;
use App\Models\TeacherClassSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentAssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'Profil siswa tidak ditemukan.');
        }

        $assignments = TeacherClassSubject::with(['subject', 'classroom', 'teacher.user'])
            ->where('classroom_id', $student->kelas_id)
            ->get();

        return view('students.assessments.index', compact('assignments', 'student'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */


    public function show(TeacherClassSubject $assignment, Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'Profil siswa tidak ditemukan.');
        }

        // pastikan ini kelasnya dia
        if ($assignment->classroom_id !== $student->kelas_id) {
            abort(403, 'Anda tidak berhak mengakses penilaian ini.');
        }

        // 🔹 semua semester (untuk isi dropdown)
        $semesters = Semester::orderBy('tahun_ajaran')
            ->orderBy('semester_ke')
            ->get();

        // 🔹 semester terpilih dari query ?semester_id=...
        $selectedSemesterId = $request->query('semester_id');

        $selectedSemester = $selectedSemesterId
            ? Semester::findOrFail($selectedSemesterId)
            : Semester::where('is_active', true)->first();

        // 🔹 ambil assessment utk mapel ini + semester terpilih + status approved
        $assessments = Assessment::where('teacher_class_subject_id', $assignment->id)
            ->where('semester_id', $selectedSemester->id)
            ->where('status', 'approved')
            ->where('is_final', false)
            ->with(['grades' => function ($q) use ($student) {
                $q->where('student_id', $student->id);
            }])
            ->orderBy('id')
            ->get();

        $finalAssessment = Assessment::where('teacher_class_subject_id', $assignment->id)
            ->where('semester_id', $selectedSemester->id)
            ->where('status', 'approved')
            ->where('is_final', true)
            ->with(['grades' => function ($q) use ($student) {
                $q->where('student_id', $student->id);
            }])
            ->first();

        $finalGrade = $finalAssessment?->grades->first()?->nilai;

        return view('students.assessments.detail', [ // atau 'show' sesuai file-mu
            'assignment'       => $assignment,
            'student'          => $student,
            'assessments'      => $assessments,
            'semesters'        => $semesters,
            'selectedSemester' => $selectedSemester,
            'finalGrade'       => $finalGrade,
        ]);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TeacherClassSubject $teacherClassSubject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeacherClassSubject $teacherClassSubject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeacherClassSubject $teacherClassSubject)
    {
        //
    }
}
