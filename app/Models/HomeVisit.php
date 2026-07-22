<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeVisit extends Model
{
    protected $fillable = [
        'student_id', 'visit_date', 'address', 'purpose',
        'result', 'conclusion', 'follow_up',
        'visitor_id', 'visitor_name', 'extra_visitors',
        'attachment',
    ];

    protected $casts = [
        'visit_date'     => 'date',
        'extra_visitors' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'visitor_id');
    }
}
