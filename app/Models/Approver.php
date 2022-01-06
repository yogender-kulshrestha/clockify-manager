<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approver extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function user()
    {
        return $this->hasOne(User::class, 'clokify_id', 'user_id');
    }
}
