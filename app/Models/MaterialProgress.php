<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialProgress extends Model
{
    protected $table = 'material_progress';

    protected $fillable = [
        'student_id',
        'material_id',
        'is_completed',
        'last_position',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the student (user) for this progress record
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the material for this progress record
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
