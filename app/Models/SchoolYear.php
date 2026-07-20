<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolYear extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'is_active'];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
    ];

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    public static function active(): ?self
    {
        return self::where('is_active', true)->first();
    }
}
