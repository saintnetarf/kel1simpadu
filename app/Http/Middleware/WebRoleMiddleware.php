<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WebRoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $allowedRoles = collect($roles)
            ->flatMap(fn (string $roleGroup) => explode(',', $roleGroup))
            ->map(fn (string $role) => trim($role))
            ->filter()
            ->values()
            ->all();

        $hasRole = $user->roles()->whereIn('name', $allowedRoles)->exists();

        if (!$hasRole) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        return $next($request);
    }
}
