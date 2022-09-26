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

use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use N1ebieski\ICore\View\Composers\Composer;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator as Url;

class LayoutComposer extends Composer
{
    /**
     * [__construct description]
     * @param Request $request [description]
     * @param Config  $config  [description]
     * @param Str     $str     [description]
     * @param Url     $url     [description]
     */
    public function __construct(
        protected Request $request,
        protected Config $config,
        protected Str $str,
        protected Url $url
    ) {
        //
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with(array_replace_recursive([
            'title' => array(),
            'desc' => array(),
            'keys' => array(),
            'index' => 'index',
            'follow' => 'follow',
            'og' => [
                'title' => null,
                'desc' => null,
                'image' => null,
                'type' => null
            ]
        ], $view->getData()) + parent::toArray());
    }

    /**
     * [makeMeta description]
     * @param  array  $input     [description]
     * @param  string $separator [description]
     * @return string            [description]
     */
    public function makeMeta(array $input, string $separator): string
    {
        return implode($separator, array_filter($input));
    }

    /**
     * Undocumented function
     *
     * @param array $input
     * @param string $separator
     * @return string
     */
    public function getMeta(array $input, string $separator): string
    {
        return $this->makeMeta($input, $separator);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->config->get('app.url') . $this->request->getRequestUri();
    }

    /**
     * [get_theme description]
     * @return string|null [description]
     */
    public function getTheme(): ?string
    {
        $themeToggle = $this->request->cookie('theme_toggle');

        return match ($themeToggle) {
            'dark' => 'dark',
            'light' => '',

            default => $this->config->get('icore.theme')
        };
    }

    /**
     * [get_stylesheet description]
     * @param string $assets
     * @return string [description]
     */
    public function getStylesheet(string $assets = 'css/vendor/icore'): string
    {
        $path = '/' . $assets . '/web/web';

        if (
            is_string($url = parse_url($this->url->current(), PHP_URL_PATH))
            && $this->str->startsWith($url, '/' . $this->config->get('icore.routes.admin.prefix'))
        ) {
            $path = '/' . $assets . '/admin/admin';
        }

        if (file_exists(public_path() . $path . '-' . $this->getTheme() . '.css')) {
            return $path . '-' . $this->getTheme() . '.css';
        }

        return $path . '.css';
    }
}
