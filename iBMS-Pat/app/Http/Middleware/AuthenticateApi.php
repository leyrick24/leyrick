<?php

namespace App\Http\Middleware;

use Closure;

class AuthenticateApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $request->header('Authorization');
        if ($request->header('Authorization') != env('APP_KEY')) {
            abort(403);
        }

        return $next($request);
    }
}
