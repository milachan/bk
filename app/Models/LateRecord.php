<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LateRecord extends Model
{
    protected $fillable = [
        'student_id', 'date', 'arrive_time', 'entry_time',
        'duration_minutes', 'reason', 'officer_id', 'officer_name', 'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }
}
