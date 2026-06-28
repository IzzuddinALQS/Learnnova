<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    // Pastikan model ini merujuk ke tabel 'schedules'
    protected $table = 'schedules';

    // Izinkan kolom-kolom ini untuk diakses
    protected $fillable = ['course_id', 'title', 'description', 'start_time', 'end_time', 'location', 'type'];
}