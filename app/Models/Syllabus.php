<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Syllabus extends Model
{
    protected $table = 'syllabus';

    protected $fillable = [
        'name',
        'instructor',  //baru
        'theme',
        'description',
        'duration_weeks',
        'created_by',
    ];
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // public function courses(): HasMany
    // {
    //     return $this->hasMany(Course::class, 'syllabus_id');
    // }
}