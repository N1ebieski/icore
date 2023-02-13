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

namespace N1ebieski\ICore\Http\Clients\Google\Recaptcha\Invisible;

use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Http\Clients\Google\Recaptcha\Invisible\Requests\VerifyRequest;
use N1ebieski\ICore\Http\Clients\Google\Recaptcha\Invisible\Responses\VerifyResponse;

class RecaptchaClient
{
    /**
     * Undocumented function
     *
     * @param App $app
     * @param Config $config
     */
    public function __construct(protected App $app, protected Config $config)
    {
        //
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
