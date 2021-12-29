<?php

namespace N1ebieski\ICore\Http\Clients\RecaptchaV2;

use N1ebieski\ICore\Http\Clients\Client;

class CheckClient extends Client
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $method = 'POST';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $uri = '/recaptcha/api/siteverify';

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
