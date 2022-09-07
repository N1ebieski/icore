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
use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Filters\Api\Category\IndexFilter;
use N1ebieski\ICore\Http\Requests\Api\Category\IndexRequest;

interface Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Category $category
     * @param IndexRequest $request
     * @param IndexFilter $filter
     * @return JsonResponse
     */
    public function index(Category $category, IndexRequest $request, IndexFilter $filter): JsonResponse;
}
