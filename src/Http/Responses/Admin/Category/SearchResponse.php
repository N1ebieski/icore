<?php

namespace N1ebieski\ICore\Http\Responses\Admin\Category;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Routing\ResponseFactory as Response;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Translation\Translator;
use N1ebieski\ICore\Http\Responses\JsonResponseFactory;

/**
 * [SearchResponse description]
 */
class SearchResponse implements JsonResponseFactory
{
    /**
     * [private description]
     * @var Collection
     */
    protected $categories;

    /**
     * [private description]
     * @var Response
     */
    protected $response;

    /**
     * [protected description]
     * @var View
     */
    protected $view;

    /**
     * [protected description]
     * @var Translator
     */
    protected $lang;

    /**
     * [__construct description]
     * @param Response $response [description]
     * @param View            $view     [description]
     * @param Translator      $lang     [description]
     */
    public function __construct(Response $response, View $view, Translator $lang)
    {
        $this->response = $response;
        $this->view = $view;
        $this->lang = $lang;
    }

    /**
     * @param Collection $categories
     *
     * @return static
     */
    public function setCategories(Collection $categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * [response description]
     * @return JsonResponse [description]
     */
    public function makeResponse() : JsonResponse
    {
        if ($this->categories->isEmpty()) {
            return $this->response->json([
                'errors' => [
                    'category' => [$this->lang->get('icore::categories.error.search')]
                ]
            ], 404);
        }

        return $this->response->json([
            'success' => '',
            'view' => $this->view->make('icore::admin.category.partials.search', [
                'categories' => $this->categories,
                'checked' => false
            ])->render()
        ]);
    }
}
