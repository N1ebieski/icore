<?php

namespace N1ebieski\ICore\Http\Controllers\Api\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Http\Resources\User\UserResource;

class UserController
{
    /**
     * Undocumented function
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        return App::make(UserResource::class, ['user' => Auth::user()])->response();
    }
}
