<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';
    protected $fillable = [
        'nama',
    ];

    public function teachingAssignments()
    {
        return $this->hasMany(TeacherClassSubject::class);
    }
}
