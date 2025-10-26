<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthTokenFromCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If Authorization header is already present, don't override it
        if ($request->hasHeader('Authorization')) {
            return $next($request);
        }

        // Get token from cookie and decode if URL-encoded
        $token = $request->cookie('auth_token');
        if ($token) {
            $token = urldecode($token);
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }

        return $next($request);
    }
}