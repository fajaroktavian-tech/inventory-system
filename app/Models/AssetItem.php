<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetItem extends Model
{
    protected $fillable = ['category_id', 'name', 'brand', 'specification'];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function assets() {
        return $this->hasMany(Asset::class);
    }
}
