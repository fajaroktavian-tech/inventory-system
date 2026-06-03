<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id', 
        'user_id', 
        'loan_date', 
        'due_date', 
        'return_date', 
        'status', 
        'notes'
    ];

    // Relasi ke Unit Aset
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // Relasi ke Peminjam (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}