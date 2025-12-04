<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'status_code' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!auth()->user()->hasAnyRole($roles)) {
            return response()->json([
                'success' => false,
                'status_code' => 403,
                'message' => 'You do not have any permission to access this endpoint',
            ], 403);
        }

        return $next($request);
    }
}
