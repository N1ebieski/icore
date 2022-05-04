<?php

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
        ]
    ];
}
