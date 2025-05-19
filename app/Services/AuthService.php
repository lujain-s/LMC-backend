<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    protected $userRepository;
    protected $roleRepository;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    public function register(array $data)
    {
        $user = $this->userRepository->createUser($data);

        $role = $this->roleRepository->findRoleById($data['role_id']);

        if (!$role) {
            throw new \Exception('Role not found or guard mismatch', 422);
        }

        $permissions = $this->roleRepository->assignRoleToUser($user, $role);
        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
            'role' => $role->name,
            'permissions' => $permissions
        ];
    }

    public function login(array $credentials)
    {
        $token = $this->userRepository->attemptLogin($credentials);

        if (!$token) {
            throw new \Exception('Invalid credentials', 401);
        }

        $user = Auth::user();
        $userData = $this->userRepository->getUserRolesAndPermissions($user);

        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'role' => $userData['roles']->first(),
                'permissions' => $userData['permissions']
            ]
        ];
    }

    public function getMyProfile($userId)
    {
        $user = User::with(['roles.permissions'])->findOrFail($userId);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name'), // get role names
            'permissions' => $user->roles->flatMap(function($role) {
                return $role->permissions->pluck('name');
            })->unique()->values(),
        ];
    }

    public function getUserProfile($id)
    {
        $user = $this->userRepository->findUserById($id);
        $userData = $this->userRepository->getUserRolesAndPermissions($user);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $userData['roles'],
            'permissions' => $userData['permissions']
        ];
    }

    public function logout()
    {
        $this->userRepository->invalidateToken(JWTAuth::getToken());
    }

    public function registerGuest(array $data)
    {
        $guestRole = $this->roleRepository->getGuestRole();
        $data['role_id'] = $guestRole->id;

        $user = $this->userRepository->createGuestUser($data);
        $this->roleRepository->assignGuestRole($user);

        $token = JWTAuth::fromUser($user);

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'role' => 'Guest',
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'role_id' => $guestRole->id
            ],
            'token' => $token
        ];
    }

}
