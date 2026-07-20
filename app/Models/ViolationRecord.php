<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViolationRecord extends Model
{
    protected $fillable = [
        'student_id', 'violation_category_id', 'date',
        'points', 'description', 'reporter_id', 'reporter_name', 'notes', 'evidence_photo',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function violationCategory(): BelongsTo
    {
        return $this->belongsTo(ViolationCategory::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}
