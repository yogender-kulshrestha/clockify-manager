<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'hourly_rate' => 'json',
        'memberships' => 'json',
        'workspace_settings' => 'json',
    ];
}

