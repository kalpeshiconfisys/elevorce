<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        try {
            if ($guard) {
                auth()->shouldUse($guard);
            }

            $user = auth()->user();

            // Check user and guard match
            if (!$user || auth($guard)->id() !== $user->id) {
                return $this->error('Unauthorized: Token.', 401);
            }

        } catch (TokenInvalidException $e) {
            return $this->error('Token is Invalid', 401);
        } catch (TokenExpiredException $e) {
            return $this->error('Token is Expired', 401);
        } catch (JWTException $e) {
            return $this->error('Token not provided', 401);
        }

        return $next($request);
    }

    protected function error($msg = null, $code = 401)
    {
        return response()->json([
            'success' => false,
            'message' => $msg,
        ], $code);
    }
}
