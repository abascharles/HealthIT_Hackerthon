<?php

namespace App\Http\Middleware;

use App\CovidConsumption;
use App\Http\Controllers\Controller;
use Closure;

class ConsumptionSubmission
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
        // if (!in_array(env('APP_LAB'), [8]) && auth()->user()->covid_consumption_allowed) {
            // Check if COVID consumption has been submitted
            $check = new Controller;
            if (!in_array(env('APP_LAB'), [7, 25]) && $check->pendingTasks()){
                return redirect('/pending');
            }
        // }
        return $next($request);
    }
}
