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

use Illuminate\Http\Request;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\View\Factory as ViewFactory;

class ThemeComponent extends Component
{
    /**
     *
     * @var array
     */
    protected array $themes = [
        'light' => 'sun',
        'dark' => 'moon'
    ];

    /**
     *
     * @param ViewFactory $view
     * @param Config $config
     * @param Request $request
     * @return void
     */
    public function __construct(
        protected ViewFactory $view,
        protected Config $config,
        protected Request $request
    ) {
        //
    }

    /**
     *
     * @return View
     */
    public function render(): View
    {
        return $this->view->make('icore::web.components.theme', [
            'themes' => $this->themes,
            'currentTheme' => $this->getCurrentTheme()
        ]);
    }

    /**
     *
     * @return string
     */
    protected function getCurrentTheme(): string
    {
        return match ($this->request->cookie('theme_toggle')) {
            'dark' => 'dark',
            'light' => 'light',

            default => !empty($this->config->get('icore.theme')) ?
                $this->config->get('icore.theme')
                : 'light'
        };
    }

    /**
     *
     * @param string $theme
     * @param string $output
     * @return string|false
     */
    public function isCurrentTheme(string $theme, string $output = 'active'): string|false
    {
        return $this->getCurrentTheme() === $theme ? $output : false;
    }
}
