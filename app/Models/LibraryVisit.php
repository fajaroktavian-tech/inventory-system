<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'visit_date',
        'visit_time',
        'purpose',
    ];

    // Relasi ke User (untuk mendapatkan nama & kelas pemohon)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}