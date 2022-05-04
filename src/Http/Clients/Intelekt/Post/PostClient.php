<?php

namespace N1ebieski\ICore\Http\Clients\Intelekt\Post;

use Illuminate\Contracts\Container\Container as App;
use N1ebieski\ICore\Http\Clients\Intelekt\Post\Requests\IndexRequest;
use N1ebieski\ICore\Http\Clients\Intelekt\Post\Responses\IndexResponse;

class PostClient
{
    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented function
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Undocumented function
     *
     * @param array $parameters
     * @return IndexResponse
     */
    public function index(array $parameters): IndexResponse
    {
        $request = $this->app->make(IndexRequest::class, [
            'parameters' => $parameters
        ]);

        return $this->app->make(IndexResponse::class, [
            'parameters' => json_decode($request()->getBody())
        ]);
    }
}
