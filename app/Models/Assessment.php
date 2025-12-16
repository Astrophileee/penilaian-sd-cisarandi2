<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $table = 'assessments';
    protected $fillable = [
        'teacher_class_subject_id',
        'semester_id',
        'tipe',
        'judul',
        'tanggal',
        'status',
        'approval_note',
    ];

    public function teacherClassSubject()
    {
        return $this->belongsTo(TeacherClassSubject::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
