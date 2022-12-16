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

namespace N1ebieski\ICore\Route\Conversions;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use N1ebieski\ICore\Route\Conversions\Interfaces\Handler;

class AddFallbackFlag implements Handler
{
    /**
     *
     * @param Request $request
     * @param Str $str
     * @return void
     */
    public function __construct(
        protected Request $request,
        protected Str $str
    ) {
        //
    }

    /**
     *
     * @param string $url
     * @param Closure $next
     * @return mixed
     */
    public function handle(string $url, Closure $next): mixed
    {
        if (!$this->request->has('fallback')) {
            return $this->addFallbackToUrl($url);
        }

        return $next($url);
    }

    /**
     *
     * @param string $url
     * @return string
     */
    protected function addFallbackToUrl(string $url): string
    {
        $parsed = parse_url($url);

        $parsed['query'] = (isset($parsed['query']) ? $parsed['query'] . '&' : '') . 'fallback=true';

        return $this->str->buildUrl($parsed);
    }
}
