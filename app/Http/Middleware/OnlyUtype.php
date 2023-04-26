<?php

namespace App\Http\Middleware;

use Closure;

class OnlyUtype
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $user_type=null, $user_type2=null, $user_type3=null, $user_type4=null)
    {
        if(auth()->user()->user_type_id != 0 && auth()->user()->user_type_id != $user_type && auth()->user()->user_type_id != $user_type2 && auth()->user()->user_type_id != $user_type3 && auth()->user()->user_type_id != $user_type4) abort(403);

        return $next($request);
    }
}
