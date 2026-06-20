<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'deadline',
        'duration_minutes',
        'max_attempts',
        'passing_score',
        'randomize',
        'show_result'
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'randomize' => 'boolean',
        'show_result' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
{
    return $this->hasMany(QuizQuestion::class);
}

}