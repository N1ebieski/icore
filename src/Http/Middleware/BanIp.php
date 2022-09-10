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

namespace N1ebieski\ICore\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\BanValue;
use Illuminate\Http\Response as HttpResponse;

class BanIp
{
    /**
     * [__construct description]
     * @param BanValue $banValue [description]
     */
    public function __construct(protected BanValue $banValue)
    {
        //
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $bans = $this->banValue->makeCache()->rememberAllIpsAsString();

        if (!empty($bans) && preg_match('/^(' . $bans . ')/i', $request->ip() ?? '')) {
            return App::abort(
                HttpResponse::HTTP_FORBIDDEN,
                'You cannot perform this action because you are banned.'
            );
        }

        return $next($request);
    }
}
