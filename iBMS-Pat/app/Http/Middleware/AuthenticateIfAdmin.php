<?php

namespace App\Http\Middleware;

use Closure;

class AuthenticateIfAdmin
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
        if (auth()->user()->USER_TYPE == 3){
            abort(401, 'Unauthorized action.');
        }

        return $next($request);
    }
}
