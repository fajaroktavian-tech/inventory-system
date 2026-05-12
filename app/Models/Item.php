<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name',
        'category_id', // Gunakan category_id sesuai migration terbaru
        'unit',
        'stock',
        'min_stock'
    ];

    public function category()
    {
        // Secara otomatis mencari category_id
        return $this->belongsTo(Category::class);
    }

    public function incomings()
    {
        return $this->hasMany(ItemIncoming::class);
    }

    public function details()
    {
        return $this->hasMany(RequestDetail::class);
    }
}