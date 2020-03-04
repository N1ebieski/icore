<?php

namespace N1ebieski\ICore\Http\ViewComponents\Map;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\View;

/**
 * [MapComponent description]
 */
class MapComponent implements Htmlable
{
    /**
     * Undocumented variable
     *
     * @var ViewFactory
     */
    protected $view;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $container_class;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $zoom;

    /**
     * Undocumented variable
     *
     * @var array|null
     */
    protected $address_marker;

    public function __construct(
        ViewFactory $view,
        string $container_class = 'map',
        int $zoom = 15, 
        array $address_marker = null
    ) {
        $this->view = $view;

        $this->container_class = $container_class;
        $this->zoom = $zoom;
        $this->address_marker = $address_marker;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return $this->view->make('icore::web.components.map.map', [
            'containerClass' => $this->container_class,
            'zoom' => $this->zoom,
            'addressMarker' => json_encode($this->address_marker)
        ]);
    }
}
