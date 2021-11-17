<?php

namespace N1ebieski\ICore\View\Composers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Contracts\Routing\UrlGenerator as Url;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\ICore\View\Composers\Composer;

class ActiveComposer extends Composer
{
    /**
     * [private description]
     * @var Request
     */
    protected $request;

    /**
     * [private description]
     * @var Str
     */
    protected $str;

    /**
     * [private description]
     * @var Url
     */
    protected $url;

    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Str $str
     * @param Url $url
     * @param App $app
     */
    public function __construct(Request $request, Str $str, Url $url, App $app)
    {
        $this->request = $request;
        $this->str = $str;
        $this->url = $url;
        $this->app = $app;
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
            if ($this->str->contains($this->request->route()->getName(), $string)) {
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
