<?php

namespace App\Http\Middleware;

use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Contracts\Providers\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JWTmiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, \Closure $next)
    {
        try {
            if (!$this->auth->parseToken()->authenticate()) {
                return response()->json([
                    'error' => 'Unauthorized.'
                ], 401);
            }

            return $next($request);
        } catch (TokenExpiredException $t) {
            try {
                return (new UserService())->refresh();

                // return response()->json([
                //     'error' => 'Token expired.'
                // ], 401);
            } catch (TokenBlacklistedException $e) {
                return response()->json([
                    'error' => 'Token blacklisted'
                ], 401);
            } catch (Exception $e) {
                return response()->json([
                    'error' => 'Something bad happened.'
                ], 409);
            }
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Unauthorized.'
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Something bad happened.'
            ], 409);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string  $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => $this->guard()->factory()->getTTL() * 60
            ],
            'status' => 200
        ], 200);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Tymon\JWTAuth\Contracts\Providers\Auth
     */
    public function guard(): Auth
    {
        return Auth::guard();
    }
}
