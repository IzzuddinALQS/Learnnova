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
     * Mendukung dua signature parameter untuk backward compatibility:
     * 1. log(string $description, string $logName, ?Model $subject, array $properties)
     * 2. log(string $description, ?Model $subject, ?array $properties, ?string $logName)
     */
    public static function log(
        string $description,
        $arg2 = null,
        $arg3 = null,
        $arg4 = null
    ): self {
        $logName = 'general';
        $subject = null;
        $properties = [];

        if ($arg2 instanceof \Illuminate\Database\Eloquent\Model) {
            $subject = $arg2;
            $properties = is_array($arg3) ? $arg3 : [];
            $logName = is_string($arg4) ? $arg4 : ($subject ? strtolower(class_basename($subject)) : 'general');
        } elseif (is_string($arg2)) {
            $logName = $arg2;
            $subject = $arg3 instanceof \Illuminate\Database\Eloquent\Model ? $arg3 : null;
            $properties = is_array($arg4) ? $arg4 : [];
        } else {
            // fallback: arg2 is null or other types
            $subject = $arg2;
            $properties = is_array($arg3) ? $arg3 : [];
            $logName = is_string($arg4) ? $arg4 : 'general';
        }

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
