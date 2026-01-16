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
            $this->ensureDefaultAssessments($assignment, $currentSemester);
        }
        $classroom = $assignment->classroom()->with(['student.user'])->first();

        $assessments = Assessment::where('teacher_class_subject_id', $assignment->id)
            ->when($currentSemester, fn($q) => $q->where('semester_id', $currentSemester->id))
            ->where('is_final', false)
            ->with('grades')
            ->orderBy('id')
            ->get();

        $finalAssessment = $currentSemester
            ? Assessment::where('teacher_class_subject_id', $assignment->id)
                ->where('semester_id', $currentSemester->id)
                ->where('is_final', true)
                ->with('grades')
                ->first()
            : null;

        $finalGrades = $finalAssessment
            ? $finalAssessment->grades->pluck('nilai', 'student_id')
            : collect();

        return view('teachers.assignments.detail', [
            'assignment'      => $assignment,
            'classroom'       => $classroom,
            'students'        => $classroom?->student ?? collect(),
            'assessments'     => $assessments,
            'currentSemester' => $currentSemester,
            'finalAssessment' => $finalAssessment,
            'finalGrades'     => $finalGrades,
        ]);
    }

    private function ensureDefaultAssessments(TeacherClassSubject $assignment, Semester $semester): void
    {
        $defaults = [
            'uts' => ['judul' => 'Asesmen Tengah Semester', 'persentase' => 25],
            'uas' => ['judul' => 'Asesmen Sumatif Akhir Semester', 'persentase' => 30],
            'absen' => ['judul' => 'Absen', 'persentase' => 10],
            'tugas' => ['judul' => 'Tugas Harian', 'persentase' => 15],
            'sikap' => ['judul' => 'Sikap', 'persentase' => 10],
        ];

        foreach ($defaults as $tipe => $data) {
            $assessment = Assessment::firstOrCreate(
                [
                    'teacher_class_subject_id' => $assignment->id,
                    'semester_id'              => $semester->id,
                    'tipe'                     => $tipe,
                ],
                [
                    'judul'         => $data['judul'],
                    'persentase'    => $data['persentase'],
                    'tanggal'       => now()->toDateString(),
                    'status'        => 'draft',
                    'approval_note' => null,
                    'is_final'      => false,
                ]
            );

            if ($assessment->persentase !== (float) $data['persentase']) {
                $assessment->update(['persentase' => $data['persentase']]);
            }

            if ($assessment->judul !== $data['judul']) {
                $legacyTitles = [
                    'Ulangan Tengah Semester',
                    'Ulangan Akhir Semester',
                    'UTS',
                    'UAS',
                ];

                if (in_array($assessment->judul, $legacyTitles, true)) {
                    $assessment->update(['judul' => $data['judul']]);
                }
            }

            if ($assessment->is_final) {
                $assessment->update(['is_final' => false]);
            }
        }
    }

    private function baseWeights(): array
    {
        return [
            'uts' => 25,
            'uas' => 30,
            'absen' => 10,
            'tugas' => 15,
            'sikap' => 10,
        ];
    }

    private function normalizeType(?string $type): string
    {
        return strtolower(trim((string) $type));
    }

    private function isDefaultType(string $type): bool
    {
        $key = $this->normalizeType($type);
        return array_key_exists($key, $this->baseWeights());
    }

    private function mapelStatus(int $assignmentId, int $semesterId): string
    {
        return Assessment::where('teacher_class_subject_id', $assignmentId)
            ->where('semester_id', $semesterId)
            ->where('is_final', false)
            ->value('status') ?? 'draft';
    }

    private function customPercentageTotal(int $assignmentId, int $semesterId, ?int $ignoreId = null): float
    {
        $query = Assessment::where('teacher_class_subject_id', $assignmentId)
            ->where('semester_id', $semesterId)
            ->where('is_final', false);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->get(['tipe', 'persentase'])
            ->reduce(function ($total, $assessment) {
                return $total + ($this->isDefaultType($assessment->tipe) ? 0 : (float) $assessment->persentase);
            }, 0.0);
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

        $mapelStatus = $this->mapelStatus($assignment->id, $currentSemester->id);
        if (in_array($mapelStatus, ['submitted', 'approved'], true)) {
            return back()->with('error', 'Penilaian mapel sudah dikirim atau disetujui.');
        }

        $validated = $request->validate([
            'tipe'       => 'required|string|max:100',
            'judul'      => 'required|string|max:255',
            'persentase' => 'required|numeric|min:0|max:10',
        ]);

        $normalizedType = $this->normalizeType($validated['tipe']);
        if (array_key_exists($normalizedType, $this->baseWeights())) {
            return back()->withErrors(['tipe' => 'Jenis aspek ini sudah tersedia.'])->withInput();
        }

        $customTotal = $this->customPercentageTotal($assignment->id, $currentSemester->id);
        if ($customTotal >= 10) {
            return back()
                ->with('error', 'Total persentase sudah 100%, tidak bisa menambah aspek baru.')
                ->withInput();
        }

        if (($customTotal + (float) $validated['persentase']) > 10) {
            return back()
                ->with('error', 'Total persentase melebihi 100%.')
                ->withInput();
        }

        Assessment::create([
            'teacher_class_subject_id' => $assignment->id,
            'semester_id'              => $currentSemester->id,
            'tipe'                     => $validated['tipe'],
            'judul'                    => $validated['judul'],
            'persentase'               => $validated['persentase'],
            'tanggal'                  => now()->toDateString(),
            'status'                   => $mapelStatus,
            'approval_note'            => null,
            'is_final'                 => false,
        ]);

        return redirect()
            ->route('teachers.assignments.show', $assignment->id)
            ->with('success', 'Aspek penilaian berhasil dibuat.');
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

        if (in_array($assessment->status, ['submitted', 'approved'], true)) {
            return back()->with('error', 'Penilaian mapel sudah dikirim atau disetujui.');
        }

        $validated = $request->validate([
            'tipe'       => 'required|string|max:100',
            'judul'      => 'required|string|max:255',
            'persentase' => 'required|numeric|min:0|max:100',
        ]);

        $updateData = [
            'judul' => $validated['judul'],
        ];

        if (!$this->isDefaultType($assessment->tipe)) {
            $normalizedType = $this->normalizeType($validated['tipe']);
            if (array_key_exists($normalizedType, $this->baseWeights())) {
                return back()->withErrors(['tipe' => 'Jenis aspek ini sudah tersedia.'])->withInput();
            }

            $customTotal = $this->customPercentageTotal(
                $assessment->teacher_class_subject_id,
                $assessment->semester_id,
                $assessment->id
            );
            if (($customTotal + (float) $validated['persentase']) > 10) {
                return back()
                    ->with('error', 'Total persentase melebihi 100%.')
                    ->withInput();
            }

            $updateData['tipe'] = $validated['tipe'];
            $updateData['persentase'] = $validated['persentase'];
        }

        $assessment->update($updateData);

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

        if (in_array($assessment->status, ['submitted', 'approved'], true)) {
            return back()->with('error', 'Penilaian mapel sudah dikirim atau disetujui.');
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

    public function submitAssessments(TeacherClassSubject $assignment)
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

        $assessments = Assessment::where('teacher_class_subject_id', $assignment->id)
            ->where('semester_id', $currentSemester->id)
            ->where('is_final', false)
            ->get();

        if ($assessments->isEmpty()) {
            return back()->with('error', 'Belum ada aspek penilaian untuk dikirim.');
        }

        $assignment->load('classroom.student');
        $students = $assignment->classroom?->student ?? collect();
        if ($students->isEmpty()) {
            return back()->with('error', 'Belum ada murid untuk dikirim.');
        }

        $finalAssessment = Assessment::where('teacher_class_subject_id', $assignment->id)
            ->where('semester_id', $currentSemester->id)
            ->where('is_final', true)
            ->with('grades')
            ->first();

        if (!$finalAssessment) {
            return back()->with('error', 'Nilai akhir belum digenerate.');
        }

        $studentIds = $students->pluck('id')->all();
        $finalFilled = $finalAssessment->grades
            ->whereNotNull('nilai')
            ->pluck('student_id')
            ->unique()
            ->all();

        if (array_diff($studentIds, $finalFilled)) {
            return back()->with('error', 'Nilai akhir belum lengkap untuk semua murid.');
        }

        Assessment::where('teacher_class_subject_id', $assignment->id)
            ->where('semester_id', $currentSemester->id)
            ->update([
                'status' => 'submitted',
                'approval_note' => null,
            ]);

        return redirect()
            ->route('teachers.assignments.show', $assignment->id)
            ->with('success', 'Penilaian mapel berhasil dikirim.');
    }

    public function generateFinalGrades(TeacherClassSubject $assignment)
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

        $assessments = Assessment::where('teacher_class_subject_id', $assignment->id)
            ->where('semester_id', $currentSemester->id)
            ->where('is_final', false)
            ->with('grades')
            ->get();

        if ($assessments->isEmpty()) {
            return back()->with('error', 'Belum ada aspek penilaian untuk dihitung.');
        }

        $baseWeights = $this->baseWeights();
        $customTotal = 0.0;
        $baseCount = 0;

        foreach ($assessments as $assessment) {
            $typeKey = $this->normalizeType($assessment->tipe);
            if (array_key_exists($typeKey, $baseWeights)) {
                $baseCount++;
            } else {
                $customTotal += (float) $assessment->persentase;
            }
        }

        if ($customTotal > 10) {
            return back()->with('error', 'Total persentase aspek tambahan melebihi 10%.');
        }

        $extraPerBase = $baseCount > 0 ? (10 - $customTotal) / $baseCount : 0;
        $weights = [];
        foreach ($assessments as $assessment) {
            $typeKey = $this->normalizeType($assessment->tipe);
            $weights[$assessment->id] = array_key_exists($typeKey, $baseWeights)
                ? $baseWeights[$typeKey] + $extraPerBase
                : (float) $assessment->persentase;
        }

        $assignment->load('classroom.student');
        $students = $assignment->classroom?->student ?? collect();
        if ($students->isEmpty()) {
            return back()->with('error', 'Belum ada murid untuk dihitung.');
        }

        $studentIds = $students->pluck('id')->all();
        foreach ($assessments as $assessment) {
            $filledStudents = $assessment->grades
                ->whereNotNull('nilai')
                ->pluck('student_id')
                ->unique()
                ->all();

            if (array_diff($studentIds, $filledStudents)) {
                return back()->with('error', 'Nilai belum lengkap untuk aspek: ' . $assessment->judul . '.');
            }
        }

        $finalAssessment = Assessment::firstOrCreate(
            [
                'teacher_class_subject_id' => $assignment->id,
                'semester_id' => $currentSemester->id,
                'is_final' => true,
            ],
            [
                'tipe' => 'nilai_akhir',
                'judul' => 'Nilai Akhir',
                'persentase' => 0,
                'tanggal' => now()->toDateString(),
                'status' => $this->mapelStatus($assignment->id, $currentSemester->id),
                'approval_note' => null,
            ]
        );

        $gradeMapByAssessment = [];
        foreach ($assessments as $assessment) {
            $gradeMapByAssessment[$assessment->id] = $assessment->grades->pluck('nilai', 'student_id');
        }

        foreach ($students as $student) {
            $total = 0.0;
            foreach ($assessments as $assessment) {
                $nilai = (float) ($gradeMapByAssessment[$assessment->id][$student->id] ?? 0);
                $weight = $weights[$assessment->id] ?? 0;
                $total += ($nilai * $weight) / 100;
            }

            Grade::updateOrCreate(
                [
                    'assessment_id' => $finalAssessment->id,
                    'student_id'    => $student->id,
                ],
                [
                    'nilai' => round($total, 2),
                ]
            );
        }

        return redirect()
            ->route('teachers.assignments.show', $assignment->id)
            ->with('success', 'Nilai akhir berhasil dihitung.');
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
