<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Syllabus extends Model
{
    use SoftDeletes;

    protected $table = 'syllabus';

    protected $fillable = [
        'name',
        'theme',
        'description',
        'duration_weeks',
        'created_by'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'syllabus_id');
    }
}
