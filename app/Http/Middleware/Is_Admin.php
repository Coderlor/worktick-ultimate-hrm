<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Is_Admin
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
		if(Auth::check() && Auth::user()->role_users_id == 1){

			return $next($request);
		}

        return redirect("/dashboard/admin");
    }
}
