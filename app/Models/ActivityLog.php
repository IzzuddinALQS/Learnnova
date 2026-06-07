<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id', 'log_name', 'description',
        'subject_type', 'subject_id',
        'causer_type', 'causer_id',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public function causer()
    {
        return $this->morphTo();
    }

    /**
     * Helper statis untuk catat aktivitas dari mana saja.
     */
    public static function log(
        string $description,
        string $logName = 'default',
        ?Model $subject = null,
        array $properties = []
    ): self {
        return self::create([
            'user_id'      => auth()->id(),
            'log_name'     => $logName,
            'description'  => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->getKey(),
            'causer_type'  => auth()->check() ? get_class(auth()->user()) : null,
            'causer_id'    => auth()->id(),
            'properties'   => $properties,
        ]);
    }
}
