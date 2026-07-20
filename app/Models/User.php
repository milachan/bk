<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'jabatan', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role?->name, $roles);
        }
        return $this->role?->name === $roles;
    }

    public function isAdmin(): bool        { return $this->hasRole('admin'); }
    public function isGuruBK(): bool       { return $this->hasRole('guru_bk'); }
    public function isGuruPiket(): bool    { return $this->hasRole('guru_piket'); }
    public function isKepalaSekolah(): bool { return $this->hasRole('kepala_sekolah'); }

    public function canEdit(): bool
    {
        return $this->hasRole(['admin', 'guru_bk']);
    }

    public function canDelete(): bool
    {
        return $this->hasRole(['admin', 'guru_bk']);
    }

    public function canManageMaster(): bool
    {
        return $this->hasRole('admin');
    }

    // Relations
    public function lateRecordsAsOfficer(): HasMany
    {
        return $this->hasMany(LateRecord::class, 'officer_id');
    }

    public function violationRecordsAsReporter(): HasMany
    {
        return $this->hasMany(ViolationRecord::class, 'reporter_id');
    }

    public function counselingsAsCounselor(): HasMany
    {
        return $this->hasMany(Counseling::class, 'counselor_id');
    }
}
