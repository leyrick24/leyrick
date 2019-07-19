<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use App\Traits\CommonFunctions;

class ModuleMiddleware
{
    use CommonFunctions;
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $modules = array_map('strtolower', app('App\Http\Controllers\DashboardController')->modules());
        $modules = str_replace(' ', '-', $modules);
        // print_r($modules);
        // die();
        // dd($request->route()->uri());
        if (!in_array($request->route()->uri(), $modules)) {
        // if (!in_array($request->route(), $this.getModules())) {
            // dd($request->ip());
            // dd($modules['USERNAME']);
            $errorMsg = 'Unauthorized access ['.ucwords(str_replace('-', ' ', $request->path())).']';
            $this->storeLogs(4, 'Manual', $errorMsg,$request->ip(),$modules['USERNAME']);
            abort(403, 'Unauthorized action.');

        }
        // if ($this->auth->getUser()->type !== "admin") {
        //     abort(403, 'Unauthorized action.');
        // }
        return $next($request);
    }
}
