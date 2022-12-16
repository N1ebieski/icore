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

namespace N1ebieski\ICore\Http\Clients\GeoIP\Requests;

use Exception;
use Torann\GeoIP\GeoIP;
use Torann\GeoIP\Location;
use N1ebieski\ICore\Http\Clients\Request;

class LocationRequest extends Request
{
    /**
     *
     * @param array $parameters
     * @param GeoIP $geoIp
     * @return void
     */
    public function __construct(
        protected array $parameters,
        protected GeoIP $geoIp
    ) {
        $this->setParameters($parameters);
    }

    /**
     *
     * @return Location
     * @throws Exception
     */
    public function makeRequest(): Location
    {
        return $this->geoIp->getLocation($this->get('ip'));
    }
}
