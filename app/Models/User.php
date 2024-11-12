<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;

use function Laravel\Prompts\password;

class User extends Authenticatable implements JWTSubject
{


    use HasApiTokens, HasFactory, Notifiable ,Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $guarded = [
        'password',
        'role'
];
    protected $fillable=[
        'name',
       'email',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function assignedByTasks()
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }


    //  jwt setting
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    // filter by role
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }
    //timestamp sittings

    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';
};
