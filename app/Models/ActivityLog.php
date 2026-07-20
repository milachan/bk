<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'module', 'record_id', 'description', 'ip_address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $action, string $module, ?int $recordId = null, ?string $description = null): void
    {
        self::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'module'      => $module,
            'record_id'   => $recordId,
            'description' => $description,
            'ip_address'  => request()->ip(),
        ]);
    }
}
