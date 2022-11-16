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

namespace N1ebieski\ICore\Loads;

use Illuminate\Http\Request;
use Illuminate\Contracts\Config\Repository as Config;

class ThemeLoad
{
    /**
     *
     * @var string
     */
    protected string $theme;

    /**
     *
     * @param Config $config
     * @param Request $request
     * @return void
     */
    public function __construct(
        protected Config $config,
        protected Request $request
    ) {
        $this->theme = !empty($this->config->get('icore.theme')) ?
            $this->config->get('icore.theme')
            : 'light';

        if (
            count($this->config->get('icore.multi_themes')) > 1
            && !empty($this->request->cookie('theme_toggle'))
            && in_array($this->request->cookie('theme_toggle'), $this->config->get('icore.multi_themes'))
        ) {
            // @phpstan-ignore-next-line
            $this->theme = $this->request->cookie('theme_toggle');
        }
    }

    /**
     *
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
    }
}
