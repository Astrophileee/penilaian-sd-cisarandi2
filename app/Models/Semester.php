<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $table = 'semesters';
    protected $fillable = [
        'tahun_ajaran',
        'semester_ke',
        'is_active'
    ];

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
}
