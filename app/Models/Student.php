<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'nis', 'nisn', 'name', 'gender', 'birth_place', 'birth_date',
        'religion', 'address', 'phone', 'parent_name', 'parent_phone',
        'class_id', 'location', 'status', 'photo',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function lateRecords(): HasMany
    {
        return $this->hasMany(LateRecord::class);
    }

    public function violationRecords(): HasMany
    {
        return $this->hasMany(ViolationRecord::class);
    }

    public function counselings(): HasMany
    {
        return $this->hasMany(Counseling::class);
    }

    public function parentMeetings(): HasMany
    {
        return $this->hasMany(ParentMeeting::class);
    }

    public function homeVisits(): HasMany
    {
        return $this->hasMany(HomeVisit::class);
    }

    public function getTotalPointsAttribute(): int
    {
        return $this->violationRecords()->sum('points');
    }
}
