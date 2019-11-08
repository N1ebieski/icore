<?php

namespace N1ebieski\ICore\Http\Responses\Web\Category;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Translation\Translator;

/**
 * [SearchResponse description]
 */
class SearchResponse
{
    /**
     * [private description]
     * @var Collection
     */
    protected $categories;

    /**
     * [private description]
     * @var JsonResponse
     */
    protected $response;

    /**
     * [protected description]
     * @var Translator
     */
    protected $lang;

    /**
     * [__construct description]
     * @param ResponseFactory $response [description]
     * @param Translator     $lang     [description]
     */
    public function __construct(ResponseFactory $response, Translator $lang)
    {
        $this->response = $response;
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
    public function response() : JsonResponse
    {
        if ($this->categories->isEmpty()) {
            return $this->response->json([
                'errors' => [
                    'category' => [$this->lang->trans('icore::categories.error.search')]
                ]
            ], 404);
        }

        return $this->response->json([
            'success' => '',
            'view' => $this->response->view('icore::web.category.partials.search', [
                'categories' => $this->categories,
                'checked' => false
            ])->render()
        ]);
    }
}
