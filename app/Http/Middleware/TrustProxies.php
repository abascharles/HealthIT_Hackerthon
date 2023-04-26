<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Fideloper\Proxy\TrustProxies as Middleware;

// use Closure;

class TrustProxies extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    // public function handle($request, Closure $next)
    // {

    //     return $next($request);
    // }

    protected $proxies = '*';

    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}
