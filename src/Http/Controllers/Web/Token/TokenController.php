<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Token;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Http\Requests\Web\Token\StoreRequest;
use N1ebieski\ICore\Models\Token\PersonalAccessToken as Token;

class TokenController
{
    /**
     * Undocumented function
     *
     * @param Token $token
     * @return JsonResponse
     */
    public function create(Token $token): JsonResponse
    {
        $abilities = Collect::make($token::$abilities);

        return Response::json([
            'view' => View::make('icore::web.token.create', [
                'abilities' => $abilities,
                'col_count' => (int)ceil($abilities->count() / 3)
            ])->render()
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Token $token
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(Token $token, StoreRequest $request): JsonResponse
    {
        [$accessToken, $refreshToken] = $token->makeService()->create($request->validated());

        $request->session()->flash('success', Lang::get('icore::tokens.success.store'));
        $request->session()->flash('accessToken', $accessToken->plainTextToken);

        return Response::json([], HttpResponse::HTTP_CREATED);
    }

    /**
     * Undocumented function
     *
     * @return JsonResponse
     */
    public function destroy(Token $token): JsonResponse
    {
        $token->makeService()->delete();

        return Response::json([], HttpResponse::HTTP_NO_CONTENT);
    }
}
