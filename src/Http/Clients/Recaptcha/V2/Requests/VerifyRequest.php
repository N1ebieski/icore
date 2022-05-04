<?php

namespace N1ebieski\ICore\Http\Clients\Recaptcha\V2\Requests;

use Psr\Http\Message\ResponseInterface;
use N1ebieski\ICore\Http\Clients\Recaptcha\Request;

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
    public function __invoke(): ResponseInterface
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
