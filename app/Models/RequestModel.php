<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestModel extends Model
{
    protected $table = 'requests';
    protected $fillable = ['user_id', 'type', 'class_id', 'room_id', 'status', 'request_date', 'approved_by', 'approved_at', 'notes'];

    public function details()
    {
        return $this->hasMany(RequestDetail::class, 'request_id');
    }
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
    
}
