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
            ->where('status', 'submitted')
            ->orderBy('tanggal')
            ->get();

        return view('headmasters.assessments.index', compact('assessments'));
    }

        public function updateStatus(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'status'        => 'required|string',
            'approval_note' => 'nullable|string',
        ]);

        $assessment->status = $validated['status'];
        $assessment->approval_note = $validated['status'] === 'rejected'
            ? ($validated['approval_note'] ?? null)
            : null;

        $assessment->save();

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
