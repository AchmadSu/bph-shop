<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;

class JwtFromCookie
{
    public function handle($request, Closure $next)
    {
        $token = $request->cookie('access_token');

        if (!$token) {
            return response()->json(['error' => 'Unauthorized, token missing'], 401);
        }

        try {
            $jwt = JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
            }

            Auth::login($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token invalid or expired'], 401);
        }

        return $next($request);
    }
}
