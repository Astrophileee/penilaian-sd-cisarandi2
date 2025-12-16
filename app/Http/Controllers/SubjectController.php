<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::all();

        return view('subjects.index', compact('subjects'));
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
            'nama' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $subject = Subject::create([
                'nama' => $validated['nama'],
            ]);

        DB::commit();

            return redirect()->route('subjects.index')->with('success', 'Data mata pelajaran berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan data mata pelajaran.'])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'nama'          => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $subject) {
            $subject->update([
                'nama'      => $validated['nama'],
            ]);
        });

        return redirect()->route('subjects.index')->with('success', 'Data Mata Pelajaran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        try {
            $subject->delete();
            return redirect()->route('subjects.index')->with('success', 'Mata Pelajaran berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->route('subjects.index')->with('error', 'Mata Pelajaran tidak dapat dihapus karena masih digunakan di data/transaksi lain.');
        }
    }
}
