<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;


use Illuminate\Support\Collection;


//class User extends Authenticatable
class User extends Authenticatable implements JWTSubject{

    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'api'; // Important for Spatie with JWT

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'remember_token',
        'email_verified_at',
    ];

    public function getJWTIdentifier() { return $this->getKey(); }

    public function getJWTCustomClaims() { return []; }

    public function getAllPermissions(): Collection
    {
        return $this->getPermissionsViaRoles();
    }

    /**
     * Check if user has permission.
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        return $this->hasPermissionThroughRole($permission) ||
               parent::hasPermissionTo($permission, $guardName);
    }


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

}
