<?php

namespace N1ebieski\ICore\Http\Clients\Recaptcha\Invisible;

use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Http\Clients\Recaptcha\Invisible\Requests\VerifyRequest;
use N1ebieski\ICore\Http\Clients\Recaptcha\Invisible\Responses\VerifyResponse;

class RecaptchaClient
{
    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented function
     *
     * @param App $app
     * @param Config $config
     */
    public function __construct(App $app, Config $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * Undocumented function
     *
     * @param array $parameters
     * @return VerifyResponse
     */
    public function verify(array $parameters): VerifyResponse
    {
        /**
         * @var VerifyRequest
         */
        $request = $this->app->make(VerifyRequest::class, [
            'parameters' => array_merge(
                [
                    'secret' => $this->config->get('services.recaptcha_invisible.secret_key')
                ],
                $parameters
            )
        ]);

        return $this->app->make(VerifyResponse::class, [
            'parameters' => json_decode($request->makeRequest()->getBody())
        ]);
    }
}
