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

namespace N1ebieski\ICore\Http\Controllers\Api\Tag;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\Tag\Tag;
use N1ebieski\ICore\Filters\Api\Tag\IndexFilter;
use N1ebieski\ICore\Http\Resources\Tag\TagResource;
use N1ebieski\ICore\Http\Requests\Api\Tag\IndexRequest;

/**
 * @group Tags
 *
 * > Routes:
 *
 *     /routes/vendor/icore/api/tags.php
 *
 * > Controller:
 *
 *     N1ebieski\ICore\Http\Controllers\Api\Tag\TagController
 *
 * > Resource:
 *
 *     N1ebieski\ICore\Http\Resources\Tag\TagResource
 *
 * Permissions:
 *
 * - api.* - access to all api endpoints
 * - api.tags.* - access to all tags endpoints
 * - api.tags.view - access to endpoints with collection of tags
 */
class TagController
{
    /**
     * Index of tags
     *
     * @apiResourceCollection N1ebieski\ICore\Http\Resources\Tag\TagResource
     * @apiResourceModel N1ebieski\ICore\Models\Tag\Tag
     *
     * @param Tag $tag
     * @param IndexRequest $request
     * @param IndexFilter $filter
     * @return JsonResponse
     */
    public function index(Tag $tag, IndexRequest $request, IndexFilter $filter): JsonResponse
    {
        return App::make(TagResource::class)
            ->collection(
                $tag->makeRepo()->paginateByFilter($filter->all())
            )
            ->additional(['meta' => [
                'filter' => $filter->all()
            ]])
            ->response();
    }
}
