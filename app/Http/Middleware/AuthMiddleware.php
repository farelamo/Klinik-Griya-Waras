<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class AuthMiddleware extends BaseMiddleware
{
    public function returnCondition($condition, $errorCode, $message)
    {
        return response()->json([
            'success' => $condition,
            'message' => $message,
        ], $errorCode);
    }

    public function handle(Request $request, Closure $next): Response
    {
        try {
            /* 
                Harus extend BaseMiddleware nya si TymonAuth kalo ga gitu exceptionnya ga jalan, 
                finally dia kena error di parseTokennya (cannot parse token from request) alias
                akses route tanpa token. Isi dari code 31 cuma return info dari user yang sedang login
                berdasarkan tokennya (tampil semua kecuali password).
            */
            $user = JWTAuth::parseToken()->authenticate();

        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return $this->returnCondition(false, 401, 'Token is Invalid');
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return $this->returnCondition(false, 401, 'Token is Expired');
            }else{
                return $this->returnCondition(false, 401, 'Token not found');
            }
        }
        return $next($request);
    }
}
