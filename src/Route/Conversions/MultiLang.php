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
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application as App;
use N1ebieski\ICore\Route\Conversions\Interfaces\Handler;

class MultiLang implements Handler
{
    /**
     *
     * @param Str $str
     * @param Config $config
     * @param App $app
     * @return void
     */
    public function __construct(
        protected Str $str,
        protected Config $config,
        protected App $app
    ) {
        //
    }

    /**
     *
     * @param string $url
     * @return bool
     */
    public function verify(string $url): bool
    {
        return count($this->config->get('icore.multi_langs')) > 1
            && !$this->str->match('/\/([a-z]{2})\//', $url);
    }

    /**
     *
     * @param string $url
     * @param Closure $next
     * @return mixed
     */
    public function handle(string $url, Closure $next): mixed
    {
        if ($this->verify($url)) {
            return $this->addLangToUrl($url);
        }

        return $next($url);
    }

    /**
     *
     * @param string $url
     * @return string
     */
    protected function addLangToUrl(string $url): string
    {
        $parsed = parse_url($url);

        $parsed['path'] = '/' . $this->app->getLocale() . ($parsed['path'] ?? '');
        $parsed['query'] = (isset($parsed['query']) ? $parsed['query'] . '&' : '') . 'fallback=true';

        return $this->str->buildUrl($parsed);
    }
}
