<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentMeeting extends Model
{
    protected $fillable = [
        'student_id', 'meeting_date', 'reason', 'parent_attended',
        'meeting_result', 'agreement', 'follow_up',
        'handler_id', 'handler_name', 'extra_handlers',
    ];

    protected $casts = [
        'meeting_date'    => 'date',
        'parent_attended' => 'boolean',
        'extra_handlers'  => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handler_id');
    }
}
