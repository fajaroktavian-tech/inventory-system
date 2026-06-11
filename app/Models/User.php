<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


#[Fillable([
    'name',
    'username',
    'email',
    'password',
    'role',
    'rfid_uid',
    'is_active',
    'class_id',
    'nis',
    'phone',
    'address',
    'avatar',
    'nip',
    'position',
    'avatar',
])]

#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
    public function scopeStaffOnly($query)
    {
        return $query->whereIn('role', ['guru', 'staff', 'admin', 'kesiswaan', 'piket', 'walikelas']);
    }
    public function isKesiswaan(): bool
    {
        return $this->role === 'kesiswaan';
    }

    public function isWalikelas(): bool
    {
        return $this->role === 'walikelas';
    }

    public function isPiket(): bool
    {
        return $this->role === 'piket';
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Helper untuk mendapatkan absen hari ini
    public function attendanceToday()
    {
        return $this->hasOne(Attendance::class)->where('date', now()->toDateString());
    }
}