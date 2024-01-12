<?php

namespace App\Http\Middleware;

use Closure;

class UserAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()->firstlogin === null) {
            return redirect("onboarding");
        }else{
            $this->middleware('guest')->except('logout');
        }
        return $next($request);
    }
}
