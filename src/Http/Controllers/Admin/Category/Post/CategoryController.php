<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Category\Post;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Filters\Admin\Category\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Category\SearchRequest;
use N1ebieski\ICore\Http\Responses\Admin\Category\SearchResponse;
use N1ebieski\ICore\Http\Requests\Admin\Category\Post\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\Post\StoreRequest;
use N1ebieski\ICore\Http\Controllers\Admin\Category\Post\Polymorphic;
use N1ebieski\ICore\Http\Requests\Admin\Category\Post\StoreGlobalRequest;
use N1ebieski\ICore\Http\Controllers\Admin\Category\CategoryController as BaseCategoryController;

/**
 * [CategoryController description]
 */
class CategoryController implements Polymorphic
{
    /**
     * Undocumented variable
     *
     * @var BaseCategoryController
     */
    protected $controller;

    /**
     * Undocumented function
     *
     * @param BaseCategoryController $controller
     */
    public function __construct(BaseCategoryController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Display a listing of the Category.
     *
     * @param  Category      $category      [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return HttpResponse                 [description]
     */
    public function index(Category $category, IndexRequest $request, IndexFilter $filter) : HttpResponse
    {
        $categoryService = $category->makeService();

        return Response::view('icore::admin.category.index', [
            'model' => $category,
            'categories' => $categoryService->paginateByFilter($filter->all() + [
                'except' => $request->input('except')
            ]),
            'parents' => $categoryService->getAsFlatTree(),
            'filter' => $filter->all(),
            'paginate' => Config::get('database.paginate')
        ]);
    }

    /**
     * Show the form for creating a new Category.
     *
     * @param  Category      $category      [description]
     * @return JsonResponse
     */
    public function create(Category $category) : JsonResponse
    {
        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.category.create', [
                'model' => $category,
                'categories' => $category->makeService()->getAsFlatTree()
            ])->render()
        ]);
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param  Category      $category      [description]
     * @param  StoreRequest  $request
     * @return JsonResponse
     */
    public function store(Category $category, StoreRequest $request) : JsonResponse
    {
        $category->makeService()->create($request->only(['name', 'icon', 'parent_id']));

        $request->session()->flash(
            'success',
            Lang::get('icore::categories.success.store') . (
                $request->input('parent_id') !== null ?
                    Lang::get('icore::categories.success.store_parent', [
                        'parent' => $category->find($request->input('parent_id'))->name
                    ])
                    : null
            )
        );

        return Response::json(['success' => '' ]);
    }

    /**
     * Store collection of Categories with childrens in storage.
     *
     * @param  Category      $category      [description]
     * @param  StoreGlobalRequest  $request
     * @return JsonResponse
     */
    public function storeGlobal(Category $category, StoreGlobalRequest $request) : JsonResponse
    {
        $category->makeService()->createGlobal($request->only(['names', 'parent_id', 'clear']));

        $request->session()->flash(
            'success',
            Lang::get('icore::categories.success.store_global') . (
                $request->input('parent_id') !== null ?
                    Lang::get('icore::categories.success.store_parent', [
                        'parent' => $category->find($request->input('parent_id'))->name
                    ])
                    : null
            )
        );

        return Response::json(['success' => '' ]);
    }

    /**
     * Search Categories for specified name.
     *
     * @param  Category      $category [description]
     * @param  SearchRequest $request  [description]
     * @param  SearchResponse $response [description]
     * @return JsonResponse                [description]
     */
    public function search(Category $category, SearchRequest $request, SearchResponse $response) : JsonResponse
    {
        return $this->controller->search($category, $request, $response);
    }
}
