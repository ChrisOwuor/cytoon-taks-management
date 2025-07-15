<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {

        // Check if the user has the required role
        $user = $request->user();

        if (!$user || !in_array($user->role->value, $roles)) {
            // If the user does not have the required role, return a 403 Forbidden response
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
