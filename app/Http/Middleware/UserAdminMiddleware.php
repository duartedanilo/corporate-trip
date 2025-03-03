<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $payload = auth()->payload();

        if (!$payload['is_admin']) {
            return response()->json(['error' => 'The user can\'t access this route because is not an admin'], 401);
        }

        return $next($request);
    }
}
