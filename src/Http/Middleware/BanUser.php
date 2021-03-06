<?php

namespace N1ebieski\ICore\Http\Middleware;

use Closure;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

/**
 * Checks if the User is banned
 */
class BanUser
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
        if (Auth::check() && Auth::user()->ban) {
            return App::abort(
                HttpResponse::HTTP_FORBIDDEN,
                'You cannot perform this action because you are banned.'
            );
        }

        return $next($request);
    }
}
