<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestDetail extends Model
{
    // Opsional jika nama tabel sudah 'request_details' (jamak dari model), 
    // tapi lebih aman ditulis eksplisit:
    protected $table = 'request_details';

    protected $fillable = [
        'request_id', 
        'item_id', 
        'quantity_requested', 
        'quantity_approved'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    // RELASI KE HEADER REQUEST (Ini yang tadi hilang)
    public function request()
    {
        return $this->belongsTo(RequestModel::class, 'request_id');
    }
    
}