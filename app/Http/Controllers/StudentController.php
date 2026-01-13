<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = User::role('student')->with(['student.classroom'])->get();
        $classrooms = Classroom::all();
        return view('users.student', compact('students','classrooms'));
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
            'name'          => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'nik'           => 'required|string|max:32|unique:students,nik',
            'kelas_id'      => 'required|exists:classrooms,id',
            'tanggal_lahir' => 'required|date',
            'alamat'        => 'required|string',
            'nama_wali'     => 'required|string|max:255',
            'no_hp_wali'    => 'required|string|max:20|phone:ID',
            'status'        => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('student');

            Student::create([
                'user_id'       => $user->id,
                'nik'           => $validated['nik'],
                'kelas_id'      => $validated['kelas_id'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'alamat'        => $validated['alamat'],
                'nama_wali'     => $validated['nama_wali'],
                'no_hp_wali'    => $validated['no_hp_wali'],
                'status'        => $validated['status'],
            ]);

            DB::commit();
            return redirect()->route('students.index')->with('success', 'Murid berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan data murid.'])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $user = $student->user;

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'nik'           => [
                'required',
                'string',
                'max:32',
                Rule::unique('students', 'nik')->ignore($student->id),
            ],
            'kelas_id'      => ['required', 'exists:classrooms,id'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat'        => ['required', 'string'],
            'nama_wali'     => ['required', 'string', 'max:255'],
            'no_hp_wali'    => ['required', 'string', 'max:20', 'phone:ID'],
            'status'        => ['required', 'string'],
            'password'      => ['nullable', 'string', 'min:6'],
        ]);

        DB::transaction(function () use ($validated, $user, $student) {
            $user->name  = $validated['name'];
            $user->email = $validated['email'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();

            $student->update([
                'nik'           => $validated['nik'],
                'kelas_id'      => $validated['kelas_id'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'alamat'        => $validated['alamat'],
                'nama_wali'     => $validated['nama_wali'],
                'no_hp_wali'    => $validated['no_hp_wali'],
                'status'        => $validated['status'],
            ]);
        });

        return redirect()->route('students.index')->with('success', 'Data Murid berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        try {
            $user = $student->user;
            $student->delete();
            if ($user) {
                $user->delete();
            }

            return redirect()->route('students.index')->with('success', 'Murid berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->route('students.index')->with('error', 'Murid tidak dapat dihapus karena masih digunakan di data/transaksi lain.');
        }
    }
}
