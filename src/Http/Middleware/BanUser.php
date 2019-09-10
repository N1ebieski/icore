<?php

namespace N1ebieski\ICore\Http\Middleware;

use Closure;

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
        if (auth()->user()->ban) {
            return abort(403, 'You cannot perform this action because you are banned.');
        }

        return $next($request);
    }
}
