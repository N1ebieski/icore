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

namespace N1ebieski\ICore\Http\Controllers\Admin\Category;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Loads\Admin\Category\EditLoad;
use N1ebieski\ICore\Filters\Admin\Category\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Category\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\CreateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\UpdateRequest;
use N1ebieski\ICore\Http\Controllers\Admin\Category\Polymorphic;
use N1ebieski\ICore\View\ViewModels\Admin\Category\CreateViewModel;
use N1ebieski\ICore\Http\Requests\Admin\Category\StoreGlobalRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\UpdateStatusRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\DestroyGlobalRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\UpdatePositionRequest;
use N1ebieski\ICore\Events\Admin\Category\StoreEvent as CategoryStoreEvent;
use N1ebieski\ICore\Events\Admin\Category\UpdateEvent as CategoryUpdateEvent;

class CategoryController implements Polymorphic
{
    /**
     * Display a listing of the Category.
     *
     * @param  Category      $category      [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return HttpResponse                 [description]
     */
    public function index(Category $category, IndexRequest $request, IndexFilter $filter): HttpResponse
    {
        return Response::view('icore::admin.category.index', [
            'model' => $category,
            'categories' => $category->makeService()->paginateByFilter($filter->all()),
            'filter' => $filter->all(),
            'paginate' => Config::get('database.paginate')
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Category $category
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function create(Category $category, CreateRequest $request): JsonResponse
    {
        return Response::json([
            'view' => View::make('icore::admin.category.create', App::make(CreateViewModel::class, [
                'category' => $category
            ]))->render()
        ]);
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param  Category      $category      [description]
     * @param  StoreRequest  $request
     * @return JsonResponse
     */
    public function store(Category $category, StoreRequest $request): JsonResponse
    {
        $category->makeService()->create($request->validated());

        $parent = null;

        if (!is_null($request->input('parent_id'))) {
            /** @var Category|null */
            $parent = $category->find($request->input('parent_id'));
        }

        $request->session()->flash(
            'success',
            // @phpstan-ignore-next-line
            Lang::get('icore::categories.success.store') . (
                !is_null($request->input('parent_id')) ?
                    Lang::get('icore::categories.success.store_parent', [
                        'parent' => $parent?->name
                    ])
                    : null
            )
        );

        Event::dispatch(App::make(CategoryStoreEvent::class, ['category' => $category]));

        return Response::json([
            'redirect' => URL::route("admin.category.{$category->poli}.index", [
                'filter' => [
                    'parent' => $parent?->id,
                    'search' => "id:\"{$category->id}\""
                ]
            ])
        ]);
    }

    /**
     * Store collection of Categories with childrens in storage.
     *
     * @param  Category      $category      [description]
     * @param  StoreGlobalRequest  $request
     * @return JsonResponse
     */
    public function storeGlobal(Category $category, StoreGlobalRequest $request): JsonResponse
    {
        $category->makeService()->createGlobal($request->validated());

        if (!is_null($request->input('parent_id'))) {
            /** @var Category|null */
            $parent = $category->find($request->input('parent_id'));
        }

        $request->session()->flash(
            'success',
            // @phpstan-ignore-next-line
            Lang::get('icore::categories.success.store_global') . (
                !is_null($request->input('parent_id')) ?
                    Lang::get('icore::categories.success.store_parent', [
                        'parent' => $parent?->name
                    ])
                    : null
            )
        );

        return Response::json([]);
    }

    /**
     * Undocumented function
     *
     * @param Category $category
     * @param EditLoad $load
     * @return JsonResponse
     */
    public function edit(Category $category, EditLoad $load): JsonResponse
    {
        return Response::json([
            'view' => View::make('icore::admin.category.edit', [
                'category' => $category
            ])->render()
        ]);
    }

    /**
     * Update the specified Category in storage.
     *
     * @param  Category      $category [description]
     * @param  UpdateRequest $request  [description]
     * @return JsonResponse                [description]
     */
    public function update(Category $category, UpdateRequest $request): JsonResponse
    {
        $category->makeService()->update($request->validated());

        Event::dispatch(App::make(CategoryUpdateEvent::class, ['category' => $category]));

        return Response::json([
            'view' => View::make('icore::admin.category.partials.category', [
                'category' => $category->loadAllRels(),
                'show_ancestors' => true
            ])->render()
        ]);
    }

    /**
     * [editPosition description]
     * @param  Category     $category [description]
     * @return JsonResponse           [description]
     */
    public function editPosition(Category $category): JsonResponse
    {
        $category->siblings_count = $category->countSiblings() + 1;

        return Response::json([
            'view' => View::make('icore::admin.category.edit_position', [
                'category' => $category
            ])->render()
        ]);
    }

    /**
     * [updatePosition description]
     * @param  Category              $category [description]
     * @param  UpdatePositionRequest $request  [description]
     * @return JsonResponse                    [description]
     */
    public function updatePosition(Category $category, UpdatePositionRequest $request): JsonResponse
    {
        $category->makeService()->updatePosition($request->input('position'));

        return Response::json([
            'siblings' => $category->makeRepo()->getSiblingsAsArray() + [$category->id => $category->position],
        ]);
    }

    /**
     * Update Status attribute the specified Comment in storage.
     *
     * @param  Category            $category [description]
     * @param  UpdateStatusRequest $request  [description]
     * @return JsonResponse                        [description]
     */
    public function updateStatus(Category $category, UpdateStatusRequest $request): JsonResponse
    {
        $category->makeService()->updateStatus($request->input('status'));

        $categoryRepo = $category->makeRepo();

        return Response::json([
            'status' => $category->status->getValue(),
            // Na potrzebę jQuery pobieramy potomków i przodków, żeby na froncie
            // zaznaczyć odpowiednie rowsy jako aktywowane bądź nieaktywne
            'ancestors' => $categoryRepo->getAncestorsAsArray(),
            'descendants' => $categoryRepo->getDescendantsAsArray(),
        ]);
    }

    /**
     * Remove the specified Category from storage.
     *
     * @param  Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category): JsonResponse
    {
        // Pobieramy potomków aby na froncie jQuery wiedział jakie rowsy usunąć
        $descendants = $category->makeRepo()->getDescendantsAsArray();

        $category->makeService()->delete();

        return Response::json([
            'descendants' => $descendants,
        ]);
    }

    /**
     * Remove the collection of Categories from storage.
     *
     * @param  Category             $category [description]
     * @param  DestroyGlobalRequest $request  [description]
     * @return RedirectResponse               [description]
     */
    public function destroyGlobal(Category $category, DestroyGlobalRequest $request): RedirectResponse
    {
        $deleted = $category->makeService()->deleteGlobal($request->get('select'));

        return Response::redirectTo(URL::previous())->with(
            'success',
            Lang::get('icore::categories.success.destroy_global', [
                'affected' => $deleted
            ])
        );
    }
}
