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
    public function makeRequest(): ResponseInterface
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
