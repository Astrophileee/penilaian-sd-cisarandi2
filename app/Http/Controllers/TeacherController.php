<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = User::role('teacher')->with('teacher')->get();

        return view('users.teacher', compact('teachers'));
    }

    public function assignments()
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        if (!$teacher) {
            abort(403, 'Profil guru tidak ditemukan.');
        }
        $assignments = $user->teacher->teachingAssignments()->with(['classroom', 'subject'])->get();
        $currentSemester = Semester::where('is_active', true)->first();

        return view('teachers.assignments.index', compact('assignments', 'currentSemester'));
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
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'no_hp' => 'required|string|max:20|phone:ID',
            'nip' => 'nullable|string|max:20',
            'alamat' => 'required|string',
            'status' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
        $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make('password'),
            ]);
        $user->assignRole('teacher');

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'no_hp' => $validated['no_hp'],
            'nip' => $validated['nip'],
            'alamat' => $validated['alamat'],
            'status' => $validated['status'],
        ]);
        DB::commit();
            return redirect()->route('teachers.index')->with('success', 'Guru berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan data guru.'])->withInput();
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {

        $user = $teacher->user;
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required','email','max:255',
                Rule::unique('users', 'email')->ignore($user->id)],
            'nip'      => ['nullable','string'],
            'no_hp'    => ['required', 'string', 'max:20'],
            'alamat'   => ['required', 'string'],
            'status'   => ['required', 'string'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        DB::transaction(function () use ($validated, $user, $teacher) {
            $user->name  = $validated['name'];
            $user->email = $validated['email'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();
            $teacher->update([
                'nip'    => $validated['nip'] ?? null,
                'no_hp'  => $validated['no_hp'],
                'alamat' => $validated['alamat'],
                'status' => $validated['status'],
            ]);
        });

        return redirect()->route('teachers.index')->with('success', 'Data Guru berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        try {
        $user = $teacher->user;
        $teacher->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->route('teachers.index')->with('success', 'Guru berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->route('teachers.index')->with('error', 'Guru tidak dapat dihapus karena masih digunakan di data/transaksi lain.');
        }
    }
}
