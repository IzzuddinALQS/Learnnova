<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'syllabus_id',
        'instructor_id',
        'title',
        'description',
        'thumbnail',
        'status',
        'duration_weeks',
        'max_students',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'duration_weeks' => 'integer',
        'max_students' => 'integer',
    ];

    /**
     * Get the instructor of the course
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get all forum threads for the course
     */
    public function forumThreads()
    {
        return $this->hasMany(ForumThread::class, 'course_id');
    }
}
