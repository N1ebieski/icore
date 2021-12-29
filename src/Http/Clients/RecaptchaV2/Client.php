<?php

namespace N1ebieski\ICore\Http\Clients\RecaptchaV2;

use N1ebieski\ICore\Http\Clients\Client as BaseClient;

class Client extends BaseClient
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

    /**
     * Undocumented function
     *
     * @param string $url
     * @return static
     */
    protected function setUrl(string $url)
    {
        $this->url = $this->host . $url;

        return $this;
    }
}
