<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';
    protected $fillable = [
        'user_id',
        'kelas_id',
        'tanggal_lahir',
        'alamat',
        'nama_wali',
        'no_hp_wali',
        'status'
    ];

        public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'kelas_id');
    }
}
