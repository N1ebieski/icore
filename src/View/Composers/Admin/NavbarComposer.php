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

namespace N1ebieski\ICore\View\Composers\Admin;

use Illuminate\Support\Str;
use N1ebieski\ICore\View\Composers\Composer;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\Foundation\Application as App;

class NavbarComposer extends Composer
{
    /**
     *
     * @var string
     */
    public string $currentLang;

    /**
     *
     * @var array
     */
    public array $langs;

    /**
     *
     * @param ViewFactory $view
     * @param Config $config
     * @param URL $url
     * @param Str $str
     * @param App $app
     * @return void
     */
    public function __construct(
        protected ViewFactory $view,
        protected Config $config,
        protected URL $url,
        protected Str $str,
        protected App $app
    ) {
        $this->currentLang = $this->app->getLocale();

        $this->langs = $this->config->get('icore.multi_langs');
    }

    /**
     *
     * @param string $lang
     * @param string $output
     * @return string|false
     */
    public function isLang(string $lang, string $output = 'active'): string|false
    {
        return $this->app->getLocale() === $lang ? $output : false;
    }

    /**
     *
     * @param string $lang
     * @return string
     */
    public function getCurrentUrlWithLang(string $lang): string
    {
        return $this->str->replace("/{$this->app->getLocale()}", "/{$lang}", $this->url->full());
    }
}
