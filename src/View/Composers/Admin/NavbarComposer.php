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

use N1ebieski\ICore\Loads\ThemeLoad;
use N1ebieski\ICore\View\Composers\Composer;
use Illuminate\Contracts\Config\Repository as Config;

class NavbarComposer extends Composer
{
    /**
     *
     * @var string
     */
    public string $currentTheme;

    /**
     *
     * @var array
     */
    public array $themes;

    /**
     *
     * @param Config $config
     * @param ThemeLoad $load
     * @return void
     */
    public function __construct(
        protected Config $config,
        protected ThemeLoad $load
    ) {
        $this->currentTheme = $this->load->getTheme();

        $this->themes = $this->config->get('icore.multi_themes');
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
