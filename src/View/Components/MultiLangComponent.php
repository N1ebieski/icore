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

namespace N1ebieski\ICore\View\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\Foundation\Application as App;
use N1ebieski\ICore\Utils\Route\Interfaces\RouteRecognizeInterface;

class MultiLangComponent extends Component
{
    /**
     *
     * @param ViewFactory $view
     * @param Config $config
     * @param URL $url
     * @param Str $str
     * @param App $app
     * @param Collect $collect
     * @param RouteRecognizeInterface $routeRecognize
     * @return void
     */
    public function __construct(
        protected ViewFactory $view,
        protected Config $config,
        protected URL $url,
        protected Str $str,
        protected App $app,
        protected Collect $collect,
        protected RouteRecognizeInterface $routeRecognize
    ) {
        //
    }

    /**
     *
     * @return View
     */
    public function render(): View
    {
        return $this->view->make('icore::web.components.multi_lang', [
            'langs' => $this->config->get('icore.multi_langs'),
            'currentLang' => $this->app->getLocale()
        ]);
    }

    /**
     *
     * @param string $lang
     * @param string $output
     * @return string|false
     */
    public function isCurrentLang(string $lang, string $output = 'active'): string|false
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
        $newUrl = $this->routeRecognize->getCurrentUrlWithLang($lang);

        if (is_string($newUrl)) {
            return $newUrl;
        }

        return $this->url->route('web.home.index', ['lang' => $lang]);
    }
}
