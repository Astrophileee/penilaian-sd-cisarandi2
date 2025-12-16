<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $table = 'classrooms';
    protected $fillable = [
        'nama',
        'tingkat',
    ];
        public function student()
    {
        return $this->hasMany(Student::class, 'kelas_id');
    }

    public function teachingAssignments()
    {
        return $this->hasMany(TeacherClassSubject::class);
    }
}
