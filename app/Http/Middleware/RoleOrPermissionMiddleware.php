<?php

namespace App\Http\Middleware;
//this middleware in laravel checks for the users permissions if it allows him to access a route
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Exceptions\UnauthorizedException;

class RoleOrPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roleOrPermission, $guard = null)
    {
        $authGuard = app('auth')->guard($guard);

        //if user not logged in (guest), it throws an exception to login first
        if ($authGuard->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        /*handling roles and permissions
        'admin|editor' or ['admin', 'editor']*/
        $rolesOrPermissions = is_array($roleOrPermission)
            ? $roleOrPermission
            : explode('|', $roleOrPermission);

        //check if user has any of the roles or permssions, if not -> exception
        if (!$authGuard->user()->hasAnyRole($rolesOrPermissions) &&
            !$authGuard->user()->hasAnyPermission($rolesOrPermissions)) {
            throw UnauthorizedException::forRolesOrPermissions($rolesOrPermissions);
        }

        //this middleware allows the request to continue to the controller
        return $next($request);
    }
}

//example
/*Route::get('/admin', function() {
    // محتوى لوحة التحكم
})->middleware(['auth', 'role_or_permission:admin|edit_posts']);
 middleware مفيد عندما تريد التحقق من أن المستخدم لديه إما دور معين أو إذن معين للوصول إلى مورد ما*/
