<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
     * Pengajar utama (backward-compat, dari kolom instructor_id)
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Semua pengajar kelas (many-to-many via course_instructors)
     */
    public function instructors()
    {
        return $this->belongsToMany(User::class, 'course_instructors', 'course_id', 'user_id')
                    ->withPivot('is_primary')
                    ->withTimestamps()
                    ->orderByPivot('is_primary', 'desc');
    }

    /**
     * Pengajar utama via pivot
     */
    public function primaryInstructor()
    {
        return $this->belongsToMany(User::class, 'course_instructors', 'course_id', 'user_id')
                    ->wherePivot('is_primary', true);
    }

    /**
     * Get all forum threads for the course
     */
    public function forumThreads()
    {
        return $this->hasMany(ForumThread::class, 'course_id');
    }

    /**
     * Get all modules for this course, ordered by order column
     */
    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('order');
    }

    /**
     * Get all enrollments for this course
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function certificateSigner(): HasOne
    {
        return $this->hasOne(CertificateSigner::class);
    }

    /**
     * Check if a specific user is enrolled in this course
     */
    public function isEnrolled(int $userId): bool
    {
        return DB::table('enrollments')
            ->where('course_id', $this->id)
            ->where('student_id', $userId)
            ->where('status', 'active')
            ->exists();
    }
}
