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
use Mews\Purifier\Facades\Purifier;

class XSSProtection
{
    /**
     * Tablica zawierajca klucze requestow ktore maja byc pomijane przy strip_tags,
     * zamiast tego wykonywany jest na nich clean przez HTML Purifier
     * @var array
     */
    protected $except = ['content_html'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $input = $request->all();

        array_walk_recursive($input, function (&$value, $key) {
            if (in_array($key, $this->except, true)) {
                $value = Purifier::clean($value);
            } else {
                $value = strip_tags($value);
            }
        });

        $request->merge($input);

        return $next($request);
    }
}
