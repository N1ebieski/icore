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

namespace N1ebieski\ICore\Http\Controllers\Api\Category;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Filters\Api\Category\IndexFilter;
use N1ebieski\ICore\Http\Requests\Api\Category\IndexRequest;
use N1ebieski\ICore\Http\Controllers\Api\Category\Polymorphic;

/**
 * @group Categories
 */
class CategoryController implements Polymorphic
{
    /**
     * Index of all categories
     *
     * @bodyParam filter.status int Must be one of 1 or 0 (available only for admin.categories.view). Example: 1
     *
     * @apiResourceCollection N1ebieski\ICore\Http\Resources\Category\CategoryResource
     * @apiResourceModel N1ebieski\ICore\Models\Category\Category states=active,sentence with=langs
     *
     * @param Category $category
     * @param IndexRequest $request
     * @param IndexFilter $filter
     * @return JsonResponse
     */
    public function index(Category $category, IndexRequest $request, IndexFilter $filter): JsonResponse
    {
        /** @var Category|0|null */
        $parent = $filter->get('parent');

        return $category->makeResource()
            ->collection($category->makeCache()->rememberByFilter($filter->all()))
            ->additional(['meta' => [
                'filter' => Collect::make($filter->all())
                    ->replace([
                        'parent' => $parent instanceof Category ?
                            $parent->makeResource()
                            : $parent
                    ])
                    ->forget('category')
                    ->toArray()
            ]])
            ->response();
    }
}
