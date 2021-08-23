<?php

namespace N1ebieski\ICore\Http\Controllers\Api\Tag;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\Tag\Tag;
use N1ebieski\ICore\Filters\Api\Tag\IndexFilter;
use N1ebieski\ICore\Http\Resources\Tag\TagResource;
use N1ebieski\ICore\Http\Requests\Api\Tag\IndexRequest;

class TagController
{
    /**
     * Undocumented function
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
