<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = [
        'course_id',
        'student_id',
        'certificate_signer_id',
        'certificate_no',
        'signer_name',
        'signer_position',
        'signature_path',
        'file_path',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function signer(): BelongsTo
    {
        return $this->belongsTo(CertificateSigner::class, 'certificate_signer_id');
    }
}
