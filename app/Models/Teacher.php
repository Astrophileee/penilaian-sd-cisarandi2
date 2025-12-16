<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $table = 'teachers';
    protected $fillable = [
        'user_id',
        'nip',
        'alamat',
        'no_hp',
        'status'
    ];

        public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teachingAssignments()
    {
        return $this->hasMany(TeacherClassSubject::class);
    }

}
