<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Counseling extends Model
{
    protected $fillable = [
        'student_id', 'date', 'problem', 'result', 'solution',
        'counselor_id', 'counselor_name', 'extra_counselors',
        'attachment',
    ];

    protected $casts = [
        'date'             => 'date',
        'extra_counselors' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }
}
