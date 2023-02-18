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
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use N1ebieski\ICore\Loads\LangLoad;
use Illuminate\Support\Facades\Config;

class MultiLang
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
        if (count(Config::get('icore.multi_langs')) > 1) {
            Config::set('app.locale', $this->load->getLang());

            App::setLocale($this->load->getLang());

            URL::defaults(['lang' => $this->load->getLang()]);

            // @phpstan-ignore-next-line
            $request->route()->forgetParameter('lang');
        }

        return $next($request);
    }
}
