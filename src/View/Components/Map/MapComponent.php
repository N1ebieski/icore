<?php

namespace N1ebieski\ICore\View\Components\Map;

use Illuminate\View\View;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as ViewFactory;

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
     *
     * @return string
     */
    public function toHtml(): string
    {
        return $this->view->make('icore::web.components.map.map', [
            'containerClass' => $this->container_class,
            'zoom' => $this->zoom,
            'addressMarker' => json_encode($this->address_marker)
        ])->render();
    }
}
