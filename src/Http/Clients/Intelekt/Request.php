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

namespace N1ebieski\ICore\Http\Clients\Intelekt;

use N1ebieski\ICore\Http\Clients\Request as BaseRequest;

abstract class Request extends BaseRequest
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $host = 'https://intelekt.net.pl';

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $options = [
        'verify' => false,
        'headers' => [
            'Accept' => 'application/json',
        ],
        'timeout' => 5
    ];
}
