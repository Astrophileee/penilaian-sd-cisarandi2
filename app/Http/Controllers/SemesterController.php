<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentSemester = Semester::where('is_active', true)->first();
        $semesters = Semester::orderByDesc('tahun_ajaran')
            ->orderByDesc('semester_ke')
            ->get();

        return view('semesters.index', compact('currentSemester', 'semesters'));
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
            'tahun_ajaran' => 'required|string|max:20',
            'semester_ke'  => 'required|integer|in:1,2',
        ]);

        DB::transaction(function () use ($validated) {
            Semester::query()->update(['is_active' => false]);
            Semester::create([
                'tahun_ajaran' => $validated['tahun_ajaran'],
                'semester_ke'  => $validated['semester_ke'],
                'is_active'    => true,
            ]);
        });

        return redirect()
            ->route('semesters.index')
            ->with('success', 'Semester baru berhasil dibuat dan diaktifkan.');
    }

    public function activate(Semester $semester)
    {
        DB::transaction(function () use ($semester) {
            Semester::query()->update(['is_active' => false]);

            $semester->is_active = true;
            $semester->save();
        });

        return redirect()
            ->route('semesters.index')
            ->with('success', 'Semester berhasil diaktifkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Semester $semester)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Semester $semester)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Semester $semester)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Semester $semester)
    {
        //
    }
}
