<?php

namespace App\Http\Middleware;

use Closure;

class CovidAllowed
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
        if(in_array(env('APP_LAB'), [5,6]) && !in_array(auth()->user()->user_type_id, [0, 5, 11]) && !auth()->user()->covid_allowed) abort(403); 
        /*if(env('APP_LAB') == 3){
            $url = url()->current();
            if(!\Str::contains($url, ['covid_sample', 'covid_patient']) 
                && auth()->user()->user_type_id 
                && !auth()->user()->covid_allowed) abort(403);    
        }*/
        return $next($request);
    }
}
