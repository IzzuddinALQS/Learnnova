<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bookmark extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'student_id',
        'material_id',
        'note',
    ];

    /**
     * Get the student (user) for this bookmark
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the material for this bookmark
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
