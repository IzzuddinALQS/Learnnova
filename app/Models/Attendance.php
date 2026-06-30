<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    // Mengarahkan model ke tabel 'attendances' yang sudah ada di database
    protected $table = 'attendances';

    // Mendefinisikan kolom yang diizinkan untuk diisi (Mass Assignment)
    protected $fillable = [
        'schedule_id', 
        'course_id', 
        'student_id', 
        'status', 
        'note', 
        'attendance_date',
        'recorded_at'
    ];

    // Menghubungkan ke tabel Users (Siswa)
    public function student()
    {
        return $this->belongsTo(\App\Models\User::class, 'student_id');
    }

    // Menghubungkan ke tabel Schedules
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}