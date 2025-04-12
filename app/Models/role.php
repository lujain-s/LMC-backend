<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role as SpatieRole;


class role extends SpatieRole
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    protected $guard_name = 'api'; // Important for Spatie with JWT

     public function user()
     {
         return $this->hasMany(User::class);
     }

    }
