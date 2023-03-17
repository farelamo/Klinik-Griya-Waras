<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DoctorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->role != 'doctor')
            return response()->json([
                'success' => false,
                'message' => 'Invalid role access' 
            ], 401);

        return $next($request);
    }
}
