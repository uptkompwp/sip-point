<?php

namespace App\Http\Middleware;

use App\Enums\ContextErrorEnum;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\InvalidClaimException;
use Tymon\JWTAuth\Exceptions\PayloadException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;

class JwtAuth
{
    use ResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = FacadesJWTAuth::parseToken()->authenticate();
            if (!$user) {
                return $this->responseError(ContextErrorEnum::UNAUTHORIZED, [
                    'message' => "user not found"
                ], 401);
            }
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            if ($e instanceof InvalidClaimException) {
                return $this->responseError(ContextErrorEnum::UNAUTHORIZED, [
                    'message' => "Invalid claim token"
                ], 401);
            } else if ($e instanceof PayloadException) {
                return $this->responseError(ContextErrorEnum::UNAUTHORIZED, [
                    'message' => "payload not valid"
                ], 401);
            } else if ($e instanceof TokenBlacklistedException) {
                return $this->responseError(ContextErrorEnum::UNAUTHORIZED, [
                    'message' => "token is blacklist"
                ], 401);
            } else if ($e instanceof TokenExpiredException) {
                return $this->responseError(ContextErrorEnum::UNAUTHORIZED, [
                    'message' => "token expired"
                ], 401);
            } else if ($e instanceof TokenInvalidException) {
                return $this->responseError(ContextErrorEnum::UNAUTHORIZED, [
                    'message' => "token invalid"
                ], 401);
            } else if ($e instanceof UserNotDefinedException) {
                return $this->responseError(ContextErrorEnum::UNAUTHORIZED, [
                    'message' => "user not defined"
                ], 401);
            } else {
                return $this->responseError(ContextErrorEnum::UNAUTHORIZED, [
                    'message' => "unauthorized"
                ], 401);
            }
        }
        return $next($request);
    }
}
