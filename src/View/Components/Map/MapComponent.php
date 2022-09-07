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

namespace N1ebieski\ICore\View\Components\Map;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory as ViewFactory;

class MapComponent extends Component
{
    /**
     *
     * @param ViewFactory $view
     * @param string $containerClass
     * @param int $zoom
     * @param null|array $addressMarker
     * @return void
     */
    public function __construct(
        protected ViewFactory $view,
        protected string $containerClass = 'map',
        protected int $zoom = 15,
        protected ?array $addressMarker = null
    ) {
        //
    }

    /**
     *
     * @return View
     */
    public function render(): View
    {
        return $this->view->make('icore::web.components.map.map', [
            'containerClass' => $this->containerClass,
            'zoom' => $this->zoom,
            'addressMarker' => json_encode($this->addressMarker)
        ]);
    }
}
