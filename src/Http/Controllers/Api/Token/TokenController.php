<?php

namespace N1ebieski\ICore\Http\Controllers\Api\Token;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Http\Requests\Api\Token\StoreRequest;
use N1ebieski\ICore\Models\Token\PersonalAccessToken as Token;

/**
 * @group Tokens
 *
 * > Routes:
 *
 *     /routes/vendor/icore/api/tokens.php
 *
 * > Controller:
 *
 *     N1ebieski\ICore\Http\Controllers\Api\Token\TokenController
 *
 * Permissions:
 *
 * - api.* - access to all api endpoints
 * - api.tokens.* - access to all tokens endpoints
 * - api.tokens.create - access to create token endpoint
 * - api.tokens.delete - access to delete token endpoint
 */
class TokenController
{
    /**
     * Create token
     *
     * Create personal access token.
     *
     * @authenticated
     *
     * @response 201 scenario=success {
     *   "access_token": "1|LN8Gmqe6cRHQpPr5X5GW9wFXuqHVK09L8FSb7Ju9"
     * }
     *
     * @responseField access_token string
     *
     * @param Token $token
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(Token $token, StoreRequest $request): JsonResponse
    {
        [$accessToken, $refreshToken] = $token->makeService()->create($request->validated());

        return Response::json([
            'access_token' => $accessToken->plainTextToken,
        ], HttpResponse::HTTP_CREATED);
    }

    // /**
    //  * Undocumented function
    //  *
    //  * @return JsonResponse
    //  */
    // public function destroy(Token $token): JsonResponse
    // {
    //     $token->makeService()->delete();

    //     return Response::json([], HttpResponse::HTTP_NO_CONTENT);
    // }
}
