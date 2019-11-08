<?php

namespace N1ebieski\ICore\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Contracts\Routing\UrlGenerator as Url;

/**
 * [ActiveHelper description]
 */
class ActiveHelper
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
     * @param Request $request
     * @param Str     $str
     * @param Url     $url
     */
    public function __construct(Request $request, Str $str, Url $url)
    {
        $this->request = $request;
        $this->str = $str;
        $this->url = $url;
    }


    /**
     * [activeCookie description]
     * @param  string|array $input  [description]
     * @param  string $output [description]
     * @return string|null         [description]
     */
    public function isCookie($input, string $output = "active") : ?string
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
    function isUrl($input, string $output = "active") : ?string
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
    function isRouteContains($input, string $output = "active") : ?string
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
    function isUrlContains($input, string $output = "active") : ?string
    {
        foreach ((array)$input as $string) {
            if ($this->request->is($string)) {
                return $output;
            }
        }

        return null;
    }

    function isTheme($input, string $output = "active") : ?string
    {
        $theme = app()->make('Helpers\View')->getTheme();

        foreach ((array)$input as $string) {
            if ($theme === $string) {
                return $output;
            }
        }

        return null;
    }
}
