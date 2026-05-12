<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name'])]
class Category extends Model
{
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
