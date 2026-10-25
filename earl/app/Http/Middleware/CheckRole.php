<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated and has the required role
        if (!$request->user() || $request->user()->role !== $role) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Forbidden. You do not have permission to perform this action.'
            ], 403);
        }

        return $next($request);
    }
}