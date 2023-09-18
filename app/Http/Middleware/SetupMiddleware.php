<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetupMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $env): Response
    {
        $user = User::where('role', 'ADMIN')->first();
        switch ($env) {
            case "SETUP_PAGE":
                if ($user != null) {
                    return redirect('/');
                } else {
                    return $next($request);
                }
                break;
            case "CHECK_SETUP":
                if ($user != null) {
                    return $next($request);
                } else {
                    return redirect('/setup');
                }
                break;
        }
    }
}
