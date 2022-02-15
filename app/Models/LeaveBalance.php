<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'clockify_id');
    }

    public function leave_type()
    {
        return $this->hasOne(LeaveType::class, 'id', 'leave_type_id');
    }
}
