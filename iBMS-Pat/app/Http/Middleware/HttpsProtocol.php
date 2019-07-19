<?php
/*
* <System Name> iBMS
* <Program Name> HttpsProtocol.php
*
* <Create> 2019.01.24 OJT Jethro
*
*
*/
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class HttpsProtocol
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
        //redirect any http to https
        //change environment to 'production' once ready for production
        if (!$request->secure() && App::environment() === 'production') {
            return redirect()->secure($request->getRequestUri(), 301);
        }
        return $next($request);
    }
}
