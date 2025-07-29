<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Http\Middleware\Web;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\RateLimiter;

class RateLimiterPerHour
{
    public function handle(Request $request, Closure $next, ?int $maxAttempts = null)
    {
        if ($maxAttempts === null) {
            return $next($request);
        }

        $key = 'custom-rate-limit:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return Response::json([
                'message' => __('Too many attempts. You may try again in :minutes minutes.', [
                    'minutes' => round(RateLimiter::availableIn($key) / 60)
                ]),
            ], 429);
        }

        RateLimiter::hit($key, 3600);

        return $next($request);
    }
}
