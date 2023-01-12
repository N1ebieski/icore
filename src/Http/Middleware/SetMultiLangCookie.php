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
use Illuminate\Http\Request;
use N1ebieski\ICore\Loads\LangLoad;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;

class SetMultiLangCookie
{
    /**
     *
     * @param LangLoad $load
     * @return void
     */
    public function __construct(protected LangLoad $load)
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
    public function handle(Request $request, Closure $next)
    {
        if (
            count(Config::get('icore.multi_langs')) > 1
            && !$request->cookie('lang_toggle')
            && is_string($this->load->getLangFromUser())
        ) {
            Cookie::queue(
                Cookie::forever(
                    name: 'lang_toggle',
                    value: $this->load->getLangFromUser(),
                    httpOnly: false
                )
            );
        }

        return $next($request);
    }
}
