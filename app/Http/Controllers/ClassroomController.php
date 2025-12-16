<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classrooms = Classroom::all();

        return view('classroom.index', compact('classrooms'));
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
            'tingkat' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $classroom = Classroom::create([
                'nama' => $validated['nama'],
                'tingkat' => $validated['tingkat'],
            ]);

        DB::commit();

            return redirect()->route('classrooms.index')->with('success', 'Data kelas berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan data kelas.'])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classroom $classroom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom)
    {

        $validated = $request->validate([
            'nama'          => ['required', 'string', 'max:255'],
            'tingkat'      => ['required', 'numeric'],
        ]);

        DB::transaction(function () use ($validated, $classroom) {
            $classroom->update([
                'nama'      => $validated['nama'],
                'tingkat' => $validated['tingkat'],
            ]);
        });

        return redirect()->route('classrooms.index')->with('success', 'Data Kelas berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom)
    {
        try {
            $classroom->delete();
            return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->route('classrooms.index')->with('error', 'Kelas tidak dapat dihapus karena masih digunakan di data/transaksi lain.');
        }
    }
}
