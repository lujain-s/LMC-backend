<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserRepository
{
    public function createUser(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
    }

    public function findUserById($id)
    {
        return User::findOrFail($id);
    }

    public function getUserRolesAndPermissions(User $user)
{
    return [
        'roles' => $user->getRoleNames(), // This returns a collection
        'permissions' => $user->getAllPermissions()->pluck('name') // This returns a collection
    ];
}

    public function attemptLogin(array $credentials)
    {
        return JWTAuth::attempt($credentials);
    }

    public function invalidateToken($token)
    {
        JWTAuth::invalidate($token);
    }

    public function createGuestUser(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
            'email_verified_at' => now(),
        ]);
    }
}