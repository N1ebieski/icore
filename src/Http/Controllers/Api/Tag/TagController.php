<?php

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
     * @responseField id int
     * @responseField name string
     * @responseField slug string
     * @responseField created_at string
     * @responseField created_at_diff string
     * @responseField updated_at string
     * @responseField updated_at_diff string
     *
     * @apiResourceCollection N1ebieski\ICore\Http\Resources\Tag\TagResource
     * @apiResourceModel N1ebieski\ICore\Models\Tag\Tag
     * @apiResourceAdditional meta="Paging, filtering and sorting information"
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
