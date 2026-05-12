<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiToken;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        $token = $request->bearerToken();
        if (!$token) return response()->json(['success'=>false,'message'=>'Unauthorized'],401);
        $api = ApiToken::with('user')->where('token', hash('sha256', $token))->first();
        if (!$api || !$api->user) return response()->json(['success'=>false,'message'=>'Unauthorized'],401);
        $user = $api->user;
        $has = $user->roles()->whereHas('permissions', function($q) use ($permission){ $q->where('name',$permission); })->exists();
        if (!$has) {
            return response()->json(['success'=>false,'message'=>'Forbidden'],403);
        }
        return $next($request);
    }
}
