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


}
