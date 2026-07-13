<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolCalendar extends Model
{
    protected $fillable = ['date', 'is_holiday', 'description'];
    
    protected $casts = [
        'date' => 'date',
        'is_holiday' => 'boolean',
    ];
}