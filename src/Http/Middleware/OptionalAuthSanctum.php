<?php

namespace N1ebieski\ICore\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OptionalAuthSanctum
{
    /**
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->bearerToken()) {
            $user = Auth::guard('sanctum')->user();

            if ($user) {
                Auth::setUser($user);
            }
        }

        return $next($request);
    }
}
