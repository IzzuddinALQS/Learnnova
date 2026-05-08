<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumThread extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'forum_threads';

    protected $fillable = [
        'course_id',
        'user_id',
        'title',
        'content',
        'image',
        'is_pinned',
        'is_locked',
        'views',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'views' => 'integer',
    ];

    /**
     * Get the course that owns the thread
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user that created the thread
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all posts for the thread
     */
    public function posts()
    {
        return $this->hasMany(ForumPost::class, 'thread_id');
    }

    /**
     * Increment view counter
     */
    public function incrementViews()
    {
        $this->increment('views');
    }
}
