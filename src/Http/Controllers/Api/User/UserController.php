<?php

namespace N1ebieski\ICore\Http\Controllers\Api\User;

use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Filters\Api\User\IndexFilter;
use N1ebieski\ICore\Http\Resources\User\UserResource;
use N1ebieski\ICore\Http\Requests\Api\User\IndexRequest;

class UserController
{
    /**
     * Undocumented function
     *
     * @param User $user
     * @param IndexRequest $request
     * @param IndexFilter $filter
     * @return JsonResponse
     */
    public function index(User $user, IndexRequest $request, IndexFilter $filter): JsonResponse
    {
        return App::make(UserResource::class)
            ->collection(
                $user->makeCache()->rememberByFilter(
                    $filter->all(),
                    $request->input('page') ?? 1
                )
            )
            ->additional(['meta' => [
                'filter' => $filter->all()
            ]])
            ->response();
    }
}
