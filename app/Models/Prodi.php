<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prodi extends Model
{
    use HasFactory;

    // Menentukan kolom yang boleh diisi mass-assignment
    protected $fillable = [
        'name',
        'alias',
    ];

    /**
     * Relasi: Satu Prodi memiliki banyak Kelas
     */
    public function classes(): HasMany
    {
        // Relasi ke ClassModel menggunakan prodi_id sebagai foreign key
        return $this->hasMany(ClassModel::class, 'prodi_id');
    }
}