<?php

namespace App\Http\Middleware;

use Closure;

class FilterRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $hello)
    {
        $response = $next($request);

        $res = json_decode($response->getContent(), true)[0];

        // return $response;
        return response($hello);
    }
}
