<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    public function leave_type()
    {
        return $this->hasOne(LeaveType::class, 'id', 'leave_type_id');
    }
}
