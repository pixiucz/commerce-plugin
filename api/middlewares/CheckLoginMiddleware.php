<?php namespace Pixiu\Commerce\api\Middlewares;

use Closure;
use RainLab\User\Facades\Auth;

class CheckLoginMiddleware
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
        if (!Auth::check()){
            return response(['msg' => 'Uživatel není příhlášen.'], 401);
        };

        return $next($request);
    }

}