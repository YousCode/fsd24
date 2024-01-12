<?php

namespace App\Http\Middleware;

use App\ContactStatusEnum;
use App\Http\Controllers\AskDemoController;
use App\Models\Contact;
use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckContactApproved
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
        if (auth()->user()->firstlogin == null) {

            return redirect("onboarding");
        }
        return $next($request);
    }
}
