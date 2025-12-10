<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RolePermission;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

         $role = strtolower($user->role);
        $rolePermissions = RolePermission::where('role', $role)
            ->pluck('permission')
            ->toArray();

     
        foreach ($permissions as $permission) {
            if (!in_array($permission, $rolePermissions)) {
                abort(403, 'Unauthorized');
            }
        }

        return $next($request);
    }
}

