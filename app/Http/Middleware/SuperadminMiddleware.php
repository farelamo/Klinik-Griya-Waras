<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperadminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->role != 'superadmin')
            return response()->json([
                'success' => false,
                'message' => 'Invalid role access' 
            ], 401);

        return $next($request);
    }
}
