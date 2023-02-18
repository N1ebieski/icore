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

namespace N1ebieski\ICore\Http\Clients\Google\Recaptcha\V2\Requests;

use Psr\Http\Message\ResponseInterface;
use N1ebieski\ICore\Http\Clients\Google\Recaptcha\Request;

class VerifyRequest extends Request
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $method = 'POST';

    /**
     * Undocumented function
     *
     * @return ResponseInterface
     */
    public function makeRequest(): ResponseInterface
    {
        try {
            $response = $this->client->request(
                $this->method,
                $this->host . '/recaptcha/api/siteverify',
                array_merge($this->options, [
                    'form_params' => [
                        'secret' => $this->get('secret'),
                        'response' => $this->get('response')
                    ]
                ])
            );
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\ICore\Exceptions\Client\TransferException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $response;
    }
}
