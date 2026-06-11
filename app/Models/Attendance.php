<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id', 'date', 'time_in', 'time_out', 
        'status', 'note', 'verified_by', 'verified_at'
    ];
    
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
