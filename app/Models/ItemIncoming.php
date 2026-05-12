<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemIncoming extends Model
{
    protected $fillable = ['item_id', 'date', 'quantity', 'description', 'created_by'];

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function user() {
        return $this->belongsTo(User::class, 'created_by');
    }
}