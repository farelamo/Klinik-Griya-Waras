<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PharmacistMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->role != 'pharmacist')
            return response()->json([
                'success' => false,
                'message' => 'Invalid role access' 
            ], 401);

        return $next($request);
    }
}
