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

namespace N1ebieski\ICore\View\Composers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use N1ebieski\ICore\View\Composers\Composer;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Routing\UrlGenerator as Url;

class ActiveComposer extends Composer
{
    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Str $str
     * @param Url $url
     * @param App $app
     */
    public function __construct(
        protected Request $request,
        protected Str $str,
        protected Url $url,
        protected App $app
    ) {
        //
    }

    /**
     * [activeCookie description]
     * @param  string|array $input  [description]
     * @param  string $output [description]
     * @return string|null         [description]
     */
    public function isCookie($input, string $output = "active"): ?string
    {
        foreach ((array)$input as $cookie) {
            if ((bool)$this->request->cookie($cookie) === true) {
                return $output;
            }
        }

        return null;
    }

    /**
     * [activeUrl description]
     * @param  string|array $input  [description]
     * @param  string $output [description]
     * @return string|null         [description]
     */
    public function isUrl($input, string $output = "active"): ?string
    {
        foreach ((array)$input as $url) {
            if ((string)$url === $this->url->current()) {
                return $output;
            }
        }

        return null;
    }

    /**
     * [activeRouteContains description]
     * @param  string|array $input  [description]
     * @param  string $output [description]
     * @return string|null         [description]
     */
    public function isRouteContains($input, string $output = "active"): ?string
    {
        foreach ((array)$input as $string) {
            /** @var Route */
            $route = $this->request->route();

            if ($this->str->contains($route->getName(), $string)) {
                return $output;
            }
        }

        return null;
    }

    /**
     * [activeUrlContains description]
     * @param  string|array $input  [description]
     * @param  string $output [description]
     * @return string|null         [description]
     */
    public function isUrlContains($input, string $output = "active"): ?string
    {
        foreach ((array)$input as $string) {
            if ($this->request->is($string)) {
                return $output;
            }
        }

        return null;
    }

    /**
     * [isTheme description]
     * @param  string|array  $input  [description]
     * @param  string  $output [description]
     * @return string|null         [description]
     */
    public function isTheme($input, string $output = "active"): ?string
    {
        $theme = $this->app->make(LayoutComposer::class)->getTheme();

        foreach ((array)$input as $string) {
            if ($theme === $string) {
                return $output;
            }
        }

        return null;
    }
}
