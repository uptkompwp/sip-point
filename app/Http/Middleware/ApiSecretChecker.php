<?php

namespace App\Http\Middleware;

use App\Enums\ContextErrorEnum;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class ApiSecretChecker
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
            $api_key_env = getenv('API_SECRET');
            if ($api_key_env) {
                $api_secret_from_header = $request->header('API_KEY');
                if ($api_secret_from_header === $api_key_env) {
                    return $next($request);
                } else {
                    return $this->responseError(ContextErrorEnum::UNAUTHORIZED, ['message' => "api key not valid"], 401);
                }
            } else {
                return $this->responseError(ContextErrorEnum::UNAUTHORIZED, ['message' => "api key not exist"], 401);
            }
        } catch (\Exception $e) {
        }
    }
}
