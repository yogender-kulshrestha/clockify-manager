<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function workspace()
    {
        return $this->hasOne(Workspace::class,'clockify_id','workspace_id');
    }
}
