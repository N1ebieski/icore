<?php

namespace N1ebieski\ICore\Http\Clients\Intelekt\Post\Requests;

use Psr\Http\Message\ResponseInterface;
use N1ebieski\ICore\Http\Clients\Intelekt\Request;

class IndexRequest extends Request
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $method = 'GET';

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
                $this->host . '/api/posts/index',
                array_merge($this->options, [
                    'form_params' => [
                        'filter' => [
                            'status' => $this->get('filter.status'),
                            'orderby' => $this->get('filter.orderby'),
                            'search' => $this->get('filter.search')
                        ]
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
