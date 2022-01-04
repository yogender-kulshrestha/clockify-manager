<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSheet extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $casts=[
        'tag_ids' => 'json',
        'custom_field_values' => 'json',
    ];

    public function project()
    {
        return $this->hasOne(Project::class,'clockify_id','project_id');
    }

    public function workspace()
    {
        return $this->hasOne(Workspace::class,'clockify_id','workspace_id');
    }
}
