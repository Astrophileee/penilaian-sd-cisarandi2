<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\TeacherClassSubject;
use Illuminate\Http\Request;

class HeadmasterAssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assessments = Assessment::with([
                'teacherClassSubject.subject',
                'teacherClassSubject.classroom.student.user',
                'teacherClassSubject.teacher.user',
                'grades',
            ])
            ->where('is_final', false)
            ->where('status', 'submitted')
            ->orderBy('teacher_class_subject_id')
            ->orderBy('semester_id')
            ->orderBy('id')
            ->get();

        $assessmentGroups = $assessments->groupBy(function ($assessment) {
            return $assessment->teacher_class_subject_id . '-' . $assessment->semester_id;
        });

        $finalAssessments = collect();
        if ($assessments->isNotEmpty()) {
            $finalAssessments = Assessment::with('grades')
                ->where('is_final', true)
                ->whereIn('teacher_class_subject_id', $assessments->pluck('teacher_class_subject_id')->unique())
                ->whereIn('semester_id', $assessments->pluck('semester_id')->unique())
                ->get()
                ->keyBy(function ($assessment) {
                    return $assessment->teacher_class_subject_id . '-' . $assessment->semester_id;
                });
        }

        return view('headmasters.assessments.index', compact('assessmentGroups', 'finalAssessments'));
    }

        public function updateStatus(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'status'        => 'required|string',
            'approval_note' => 'nullable|string',
        ]);

        $approvalNote = $validated['status'] === 'rejected'
            ? ($validated['approval_note'] ?? null)
            : null;

        Assessment::where('teacher_class_subject_id', $assessment->teacher_class_subject_id)
            ->where('semester_id', $assessment->semester_id)
            ->update([
                'status' => $validated['status'],
                'approval_note' => $approvalNote,
            ]);

        return redirect()
            ->route('headmasters.assessments.index')
            ->with('success', 'Status penilaian berhasil diperbarui.');
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
    public function show(TeacherClassSubject $teacherClassSubject)
    {
        //
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
