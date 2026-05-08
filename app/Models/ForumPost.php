<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'forum_posts';

    protected $fillable = [
        'thread_id',
        'user_id',
        'parent_id',
        'content',
        'image',
        'is_approved',
        'is_solution',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_solution' => 'boolean',
    ];

    /**
     * Get the thread that owns the post
     */
    public function thread()
    {
        return $this->belongsTo(ForumThread::class, 'thread_id');
    }

    /**
     * Get the user that created the post
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent post (for nested replies)
     */
    public function parent()
    {
        return $this->belongsTo(ForumPost::class, 'parent_id');
    }

    /**
     * Get all child replies
     */
    public function replies()
    {
        return $this->hasMany(ForumPost::class, 'parent_id');
    }
}
