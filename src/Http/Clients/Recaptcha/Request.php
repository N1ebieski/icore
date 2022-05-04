<?php

namespace N1ebieski\ICore\Http\Clients\Recaptcha;

use N1ebieski\ICore\Http\Clients\Request as BaseRequest;

abstract class Request extends BaseRequest
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $host = 'https://www.google.com';

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $options = [
        'verify' => false
    ];
}
