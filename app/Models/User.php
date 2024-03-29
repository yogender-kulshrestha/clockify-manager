<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    /*protected $fillable = [
        'name',
        'email',
        'password',
    ];*/
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'memberships',
        'settings'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'memberships' => 'json',
        'settings' => 'json',
    ];

    public function employees()
    {
        return $this->hasMany(Approver::class, 'approver_id', 'clockify_id');
    }

    public function leave_balances()
    {
        return $this->hasMany(LeaveBalance::class, 'user_id', 'clockify_id');
    }

    public function leaves_balances()
    {
        return $this->hasMany(LeaveBalance::class, 'user_id', 'clockify_id')->with('leave_type')
            ->whereHas('leave_type', function ($q) {
                $q->where('balance','1');
            });
    }
}
