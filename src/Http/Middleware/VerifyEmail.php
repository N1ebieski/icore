<?php

namespace N1ebieski\ICore\Http\Middleware;

use Closure;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\App;

class VerifyEmail
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
        if (Auth::user() && !Auth::user()->hasVerifiedEmail()) {
            return $request->expectsJson() ?
                App::abort(
                    HttpResponse::HTTP_FORBIDDEN,
                    'Your email address is not verified.'
                )
                : Response::redirectToRoute('verification.notice');
        }

        return $next($request);
    }
}
