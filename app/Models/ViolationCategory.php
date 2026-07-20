<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ViolationCategory extends Model
{
    protected $fillable = ['name', 'category', 'points', 'description'];

    public function violationRecords(): HasMany
    {
        return $this->hasMany(ViolationRecord::class);
    }
}
