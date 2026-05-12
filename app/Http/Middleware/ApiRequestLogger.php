<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ApiRequestLogger
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            ActivityLog::create([
                'user_id' => optional($request->user())->id,
                'action' => $request->method() . ' ' . $request->path(),
                'payload' => json_encode($request->all()),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Exception $e) {
            // swallow logging errors
        }

        return $response;
    }
}
