<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiToken;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $token = $request->bearerToken();
        if (!$token) return response()->json(['success'=>false,'message'=>'Unauthorized'],401);
        $api = ApiToken::with('user')->where('token', hash('sha256', $token))->first();
        if (!$api || !$api->user) return response()->json(['success'=>false,'message'=>'Unauthorized'],401);
        if (!$api->user->roles()->where('name',$role)->exists()) {
            return response()->json(['success'=>false,'message'=>'Forbidden'],403);
        }
        return $next($request);
    }
}
