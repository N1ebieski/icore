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

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use N1ebieski\ICore\Loads\ThemeLoad;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\View\Factory as ViewFactory;

class MultiThemeComponent extends Component
{
    /**
     *
     * @param ViewFactory $view
     * @param Config $config
     * @param ThemeLoad $load
     * @return void
     */
    public function __construct(
        protected ViewFactory $view,
        protected Config $config,
        protected ThemeLoad $load
    ) {
        //
    }

    /**
     *
     * @return View
     */
    public function render(): View
    {
        return $this->view->make('icore::web.components.multi_theme', [
            'themes' => $this->config->get('icore.multi_themes'),
            'currentTheme' => $this->load->getTheme()
        ]);
    }

    /**
     *
     * @param string $theme
     * @param string $output
     * @return string|false
     */
    public function isCurrentTheme(string $theme, string $output = 'active'): string|false
    {
        return $this->load->getTheme() === $theme ? $output : false;
    }
}
