<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'module_id',
        'title',
        'type',
        'file_path',
        'content',
        'duration_minutes',
        'order',
        'is_preview',
    ];

    protected $casts = [
        'is_preview'       => 'boolean',
        'duration_minutes' => 'integer',
        'order'            => 'integer',
    ];

    /**
     * Get the module that owns this material
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    /**
     * Get all progress records for this material
     */
    public function progress(): HasMany
    {
        return $this->hasMany(MaterialProgress::class);
    }

    /**
     * Get all bookmarks for this material
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Get progress record for a specific student
     */
    public function progressFor(int $studentId): ?MaterialProgress
    {
        return $this->progress()->where('student_id', $studentId)->first();
    }

    /**
     * Check if this material is bookmarked by a specific student
     */
    public function isBookmarkedBy(int $studentId): bool
    {
        return $this->bookmarks()->where('student_id', $studentId)->exists();
    }

    /**
     * Check if this material is completed by a specific student
     */
    public function isCompletedBy(int $studentId): bool
    {
        return $this->progress()
            ->where('student_id', $studentId)
            ->where('is_completed', true)
            ->exists();
    }
}
