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

namespace N1ebieski\ICore\Http\Clients\GeoIP;

use Illuminate\Contracts\Container\Container as App;
use N1ebieski\ICore\Http\Clients\GeoIP\Requests\LocationRequest;
use N1ebieski\ICore\Http\Clients\GeoIP\Responses\LocationResponse;

class GeoIPClient
{
    /**
     * Undocumented function
     *
     * @param App $app
     */
    public function __construct(protected App $app)
    {
        //
    }

    /**
     * Undocumented function
     *
     * @param array $parameters
     * @return LocationResponse
     */
    public function location(array $parameters): LocationResponse
    {
        /**
         * @var LocationRequest
         */
        $request = $this->app->make(LocationRequest::class, [
            'parameters' => $parameters
        ]);

        /**
         * @var LocationResponse
         */
        $response = $this->app->make(LocationResponse::class, [
            'response' => $request->makeRequest()
        ]);

        return $response;
    }
}
