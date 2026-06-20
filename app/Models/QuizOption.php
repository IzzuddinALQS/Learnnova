<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizOption extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
        'order'
    ];

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class);
    }
}