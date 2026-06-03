<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassModel extends Model
{
    protected $fillable = ['name','prodi_id'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'class_id');
    }

    // Tambahkan ini di dalam class ClassModel
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }
}