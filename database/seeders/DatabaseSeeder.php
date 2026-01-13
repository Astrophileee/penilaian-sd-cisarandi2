<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $headmasterRole = Role::firstOrCreate(['name' => 'headmaster']);

        //Buat admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
            ]
        );
        $admin->assignRole($adminRole);

        $headmaster = User::firstOrCreate(
            ['email' => 'headmaster@example.com'],
            [
                'name' => 'Headmaster',
                'password' => bcrypt('password'),
            ]
        );
        $headmaster->assignRole($headmasterRole);

        //Buat kelas
        $classroom = Classroom::firstOrCreate(
            [
                'nama' => '1A',
                'tingkat' => 1,
            ]
        );

        //Buat student
        $studentUser = User::firstOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Student User',
                'password' => bcrypt('password'),
            ]
        );
        $studentUser->assignRole($studentRole);

        //Buat data student
        Student::firstOrCreate(
            [
                'user_id' => $studentUser->id,
                'kelas_id' => $classroom->id,
                'tanggal_lahir' => '2025-09-12',
                'alamat' => 'ALAMAT 1',
                'nama_wali' => 'Bambang',
                'no_hp_wali' => '081234567891',
                'status' => 'aktif',
                'nik' => '3012345678910'
            ]
        );

        //Buat teacher
        $teacherUser = User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'Teacher User',
                'password' => bcrypt('password'),
            ]
        );
        $teacherUser->assignRole($teacherRole);

        //Buat data teacher
        Teacher::firstOrCreate(
            [
                'user_id' => $teacherUser->id,
                'nip' => '12312312312',
                'alamat' => 'ALAMAT TEACHER',
                'no_hp' => '081234567891',
                'status' => 'aktif',
            ]
        );
        // Buat Semester awal
        Semester::firstOrCreate([
            'tahun_ajaran' => '2025/2026',
            'semester_ke'  => 1,
            'is_active' => true,
            ]
        );
    }
}
