<?php

namespace N1ebieski\ICore\Http\Clients;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

abstract class Client
{
    /**
     * Undocumented variable
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * Undocumented variable
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $method;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $url;

    /**
     * Undocumented variable
     *
     * @var object|string
     */
    protected $contents;

    /**
     * Undocumented function
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Undocumented function
     *
     * @param string $method
     * @return static
     */
    protected function setMethod(string $method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @return static
     */
    protected function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    protected function setQueryFromParams(array $params)
    {
        foreach ($params as $key => $value) {
            if (is_int($key)) {
                $this->url = preg_replace('/({[a-z0-9]+})/', $value, $this->url, 1);

                unset($params[$key]);
            } else {
                if (strpos($this->url, '{' . $key . '}') === false) {
                    continue;
                }

                $this->url = str_replace('{' . $key . '}', $value, $this->url);

                unset($params[$key]);
            }
        }

        if ($this->method === 'GET' && !empty($params)) {
            $this->url .= '?' . http_build_query($params);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $params
     * @return static
     */
    public function setParams(array $params)
    {
        $this->setQueryFromParams($params);

        if (!isset($this->options['form_params'])) {
            $this->options['form_params'] = [];
        }

        $this->options['form_params'] = $params;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $headers
     * @return static
     */
    protected function setHeaders(array $headers)
    {
        if (!isset($this->options['headers'])) {
            $this->options['headers'] = [];
        }

        $this->options['headers'] = array_replace_recursive(
            $this->options['headers'],
            $headers
        );

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param ResponseInterface $response
     * @return static
     */
    protected function setResponse(ResponseInterface $response)
    {
        $this->response = $response;

        $this->setContentsFromResponse($response);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param ResponseInterface $response
     * @return static
     */
    protected function setContentsFromResponse(ResponseInterface $response)
    {
        $this->contents = json_decode($response->getBody());

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Undocumented function
     *
     * @return object|string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Undocumented function
     *
     * @param string $method
     * @param string $url
     * @param array|null $params
     * @return object|string
     */
    public function request(string $method, string $url, array $params = null)
    {
        $this->setMethod($method);
        $this->setUrl($url);

        if (!empty($params)) {
            $this->setParams($params);
        }

        $this->setResponse(
            $this->makeResponse()
        );

        return $this->getContents();
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param array|null $params
     * @return object|string
     */
    public function get(string $url, array $params = null)
    {
        return $this->request('GET', $url, $params);
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param array|null $params
     * @return object|string
     */
    public function post(string $url, array $params = null)
    {
        return $this->request('POST', $url, $params);
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param array|null $params
     * @return object|string
     */
    public function patch(string $url, array $params = null)
    {
        return $this->request('PATCH', $url, $params);
    }

    /**
     * Undocumented function
     *
     * @return ResponseInterface
     */
    protected function makeResponse(): ResponseInterface
    {
        try {
            $response = $this->client->request($this->method, $this->url, $this->options);
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
