<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherClassSubject;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherClassSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = TeacherClassSubject::with(['teacher.user','classroom','subject'])->get();
        $teachers = Teacher::with('user')->get();
        $classrooms = Classroom::all();
        $subjects = Subject::all();
        return view('assignments.index', compact('assignments','teachers','classrooms','subjects'));
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
        $validated = $request->validate([
            'teacher_id'      => 'required|exists:teachers,id',
            'classroom_id'      => 'required|exists:classrooms,id',
            'subject_id'      => 'required|exists:subjects,id',
        ]);

        DB::beginTransaction();

        try {
            $assignment = TeacherClassSubject::create([
                'teacher_id' => $validated['teacher_id'],
                'classroom_id' => $validated['classroom_id'],
                'subject_id' => $validated['subject_id'],
            ]);

        DB::commit();

            return redirect()->route('assignments.index')->with('success', 'Data penugasan berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan data penugasan.'])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TeacherClassSubject $assignment)
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        if (!$teacher || $assignment->teacher_id !== $teacher->id) {
            abort(403, 'Anda tidak berhak mengakses penugasan ini.');
        }
        $currentSemester = Semester::where('is_active', true)->first();
        if ($currentSemester) {
            $this->defaultUjian($assignment, $currentSemester);
        }
        $classroom = $assignment->classroom()->with(['student.user'])->first();

        $assessments = Assessment::where('teacher_class_subject_id', $assignment->id)
        ->when($currentSemester, fn($q) => $q->where('semester_id', $currentSemester->id))
        ->with('grades')
        ->orderBy('tanggal')
        ->get();

        return view('teachers.assignments.detail', [
            'assignment'      => $assignment,
            'classroom'       => $classroom,
            'students'        => $classroom?->student ?? collect(),
            'assessments'     => $assessments,
            'currentSemester' => $currentSemester,
        ]);
    }

    private function defaultUjian(TeacherClassSubject $assignment, Semester $semester): void
    {
        $defaults = [
            'uts' => 'Ulangan Tengah Semester',
            'uas' => 'Ulangan Akhir Semester',
        ];

        foreach ($defaults as $tipe => $judul) {
            Assessment::firstOrCreate(
                [
                    'teacher_class_subject_id' => $assignment->id,
                    'semester_id'              => $semester->id,
                    'tipe'                     => $tipe,
                ],
                [
                    'judul'         => $judul,
                    'tanggal'       => now()->toDateString(),
                    'status'        => 'draft',
                    'approval_note' => null,
                ]
            );
        }
    }


    public function storeAssessment(Request $request, TeacherClassSubject $assignment)
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        if (!$teacher || $assignment->teacher_id !== $teacher->id) {
            abort(403, 'Anda tidak berhak mengakses penugasan ini.');
        }

        $currentSemester = Semester::where('is_active', true)->first();
        if (!$currentSemester) {
            return back()->with('error', 'Semester aktif belum diatur.');
        }

        $validated = $request->validate([
            'type'    => 'required|string',
            'judul'   => 'required|string|max:255',
            'tanggal' => 'required|date',
        ]);

        Assessment::create([
            'teacher_class_subject_id' => $assignment->id,
            'semester_id'              => $currentSemester->id,
            'tipe'                     => $validated['type'],
            'judul'                    => $validated['judul'],
            'tanggal'                  => $validated['tanggal'],
            'status'                   => 'draft',
            'approval_note'            => null,
        ]);

        return redirect()
            ->route('teachers.assignments.show', $assignment->id)
            ->with('success', 'Tugas harian berhasil dibuat.');
    }

    public function updateAssessment(Request $request, Assessment $assessment)
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        if (
            !$teacher ||
            $assessment->teacherClassSubject->teacher_id !== $teacher->id
        ) {
            abort(403, 'Anda tidak berhak mengubah penilaian ini.');
        }

        $validated = $request->validate([
            'judul'   => 'nullable|string|max:255',
            'tanggal' => 'required|date',
            'status' => 'required|string',
        ]);

        $assessment->update([
            'judul'   => $validated['judul'] ?? $assessment->judul,
            'tanggal' => $validated['tanggal'],
            'status' => $validated['status'] ?? $assessment->status,
        ]);

        return redirect()
            ->route('teachers.assignments.show', $assessment->teacher_class_subject_id)
            ->with('success', 'Penilaian berhasil diperbarui.');
    }

    public function storeGrades(Request $request, Assessment $assessment)
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        if (
            !$teacher ||
            $assessment->teacherClassSubject->teacher_id !== $teacher->id
        ) {
            abort(403, 'Anda tidak berhak mengakses penilaian ini.');
        }

        $validated = $request->validate([
            'nilai'   => 'required|array',
            'nilai.*' => 'nullable|numeric|min:0|max:100',
        ]);

        $nilaiData = $validated['nilai'];

        foreach ($nilaiData as $studentId => $nilai) {
            if ($nilai === null || $nilai === '') {
                continue;
            }

            Grade::updateOrCreate(
                [
                    'assessment_id' => $assessment->id,
                    'student_id'    => $studentId,
                ],
                [
                    'nilai'   => $nilai,
                ]
            );
        }

        return redirect()
            ->route('teachers.assignments.show', $assessment->teacher_class_subject_id)
            ->with('success', 'Nilai siswa berhasil disimpan.');
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
    public function update(Request $request, TeacherClassSubject $assignment)
    {
        $validated = $request->validate([
            'teacher_id'      => ['required', 'exists:teachers,id'],
            'classroom_id'      => ['required', 'exists:classrooms,id'],
            'subject_id'      => ['required', 'exists:subjects,id'],
        ]);

        DB::transaction(function () use ($validated, $assignment) {
            $assignment->update([
                'teacher_id'      => $validated['teacher_id'],
                'classroom_id'      => $validated['classroom_id'],
                'subject_id'      => $validated['subject_id'],
            ]);
        });

        return redirect()->route('assignments.index')->with('success', 'Data Penugasan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeacherClassSubject $assignment)
    {
        try {
            $assignment->delete();
            return redirect()->route('assignments.index')->with('success', 'Penugasan berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->route('assignments.index')->with('error', 'Penugasan tidak dapat dihapus karena masih digunakan di data/transaksi lain.');
        }
    }
}
