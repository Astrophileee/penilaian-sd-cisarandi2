<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Semester;
use App\Models\TeacherClassSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherAssessmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            abort(403, 'Profil guru tidak ditemukan.');
        }

        $assignments = TeacherClassSubject::with(['subject', 'classroom', 'teacher.user'])
            ->where('teacher_id', $teacher->id)
            ->get();

        return view('teachers.assessments.index', compact('assignments', 'teacher'));
    }

    public function show(TeacherClassSubject $assignment, Request $request)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher || $assignment->teacher_id !== $teacher->id) {
            abort(403, 'Anda tidak berhak mengakses penilaian ini.');
        }

        $semesters = Semester::orderBy('tahun_ajaran')
            ->orderBy('semester_ke')
            ->get();

        $selectedSemesterId = $request->query('semester_id');
        $selectedSemester = $selectedSemesterId
            ? Semester::findOrFail($selectedSemesterId)
            : Semester::where('is_active', true)->first();

        if (!$selectedSemester && $semesters->isNotEmpty()) {
            $selectedSemester = $semesters->first();
        }

        $classroom = $assignment->classroom()->with(['student.user'])->first();
        $students = $classroom?->student ?? collect();

        $selectedStudentId = $request->query('student_id');
        $selectedStudent = $selectedStudentId
            ? $students->firstWhere('id', (int) $selectedStudentId)
            : $students->first();

        if ($selectedStudentId && !$selectedStudent) {
            abort(404, 'Siswa tidak ditemukan.');
        }

        $assessments = collect();
        $finalGrade = null;
        if ($selectedSemester && $selectedStudent) {
            $assessments = Assessment::where('teacher_class_subject_id', $assignment->id)
                ->where('semester_id', $selectedSemester->id)
                ->where('status', 'approved')
                ->where('is_final', false)
                ->with(['grades' => function ($q) use ($selectedStudent) {
                    $q->where('student_id', $selectedStudent->id);
                }])
                ->orderBy('id')
                ->get();

            $finalAssessment = Assessment::where('teacher_class_subject_id', $assignment->id)
                ->where('semester_id', $selectedSemester->id)
                ->where('status', 'approved')
                ->where('is_final', true)
                ->with(['grades' => function ($q) use ($selectedStudent) {
                    $q->where('student_id', $selectedStudent->id);
                }])
                ->first();

            $finalGrade = $finalAssessment?->grades->first()?->nilai;
        }

        return view('teachers.assessments.detail', [
            'assignment'       => $assignment,
            'teacher'          => $teacher,
            'students'         => $students,
            'selectedStudent'  => $selectedStudent,
            'assessments'      => $assessments,
            'semesters'        => $semesters,
            'selectedSemester' => $selectedSemester,
            'finalGrade'       => $finalGrade,
        ]);
    }
}
