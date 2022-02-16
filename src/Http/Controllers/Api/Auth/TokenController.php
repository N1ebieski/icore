<?php

namespace N1ebieski\ICore\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Http\Requests\Api\Auth\RevokeRequest;
use N1ebieski\ICore\Http\Controllers\Auth\LoginController;
use N1ebieski\ICore\Http\Requests\Api\Auth\RefreshRequest;
use N1ebieski\ICore\Models\Token\PersonalAccessToken as Token;

/**
 * @group Authentication
 *
 * > Routes:
 *
 *     /routes/vendor/icore/api/auth.php
 *
 * > Controllers:
 *
 *     N1ebieski\ICore\Http\Controllers\Api\Auth\RegisterController
 *     N1ebieski\ICore\Http\Controllers\Api\Auth\TokenController
 *
 */
class TokenController
{
    /**
     * Undocumented variable
     *
     * @var LoginController
     */
    protected $decorated;

    /**
     * Undocumented function
     *
     * @param LoginController $decorated
     */
    public function __construct(LoginController $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * Token
     *
     * Create access token and (optional) refresh token.
     *
     * <aside class="notice">Access token expires after 2 hours. Refresh token expires after 1 year.</aside>
     *
     * @unauthenticated
     *
     * @bodyParam email string required Example: kontakt@demo.icore.intelekt.net.pl
     * @bodyParam password string required Example: demo1234
     * @bodyParam remember boolean Example: true
     *
     * @response 201 scenario=success {
     *  "access_token": "1|LN8Gmqe6cRHQpPr5X5GW9wFXuqHVK09L8FSb7Ju9",
     *  "refresh_token": "2|hVHAhrgiBmSbyYjbVAC17wjwAJyKK8TQmhglBAtM"
     * }
     *
     * @responseField access_token string
     * @responseField refresh_token string (only if remember param was true)
     *
     * @param Token $token
     * @param Request $request
     * @return JsonResponse
     */
    public function token(Token $token, Request $request): JsonResponse
    {
        // API for token guard is stateless but we use validate login logic from laravel/ui
        $request->setLaravelSession(optional());

        $this->decorated->login($request);

        [$accessToken, $refreshToken] = $token->makeService()->create([
            'name' => 'login',
            'abilities' => ['api.*'],
            'expiration' => Config::get('sanctum.access_expiration'),
            'refresh' => filter_var($request->input('remember'), FILTER_VALIDATE_BOOLEAN) ?: null
        ]);

        // We have to remove cookie credentials
        $this->decorated->logout($request);

        return Response::json(array_filter([
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => isset($refreshToken) ? $refreshToken->plainTextToken : null
        ]), HttpResponse::HTTP_CREATED);
    }

    /**
     * Refresh token
     *
     * Create new access token and refresh token.
     *
     * <aside class="success">Refresh token must be in the Authorization header with the value "Bearer {YOUR_REFRESH_TOKEN}".</aside>
     *
     * @authenticated
     *
     * @header Authorization Bearer {YOUR_REFRESH_TOKEN}
     *
     * @response 201 scenario=success {
     *  "access_token": "3|LN8Gmqe6cRHQpPr5X5GW9wFXuqHVK09L8FSb7Ju9",
     *  "refresh_token": "4|hVHAhrgiBmSbyYjbVAC17wjwAJyKK8TQmhglBAtM"
     * }
     *
     * @responseField access_token string
     * @responseField refresh_token string
     *
     * @param Token $token
     * @param RefreshRequest $request
     * @return JsonResponse
     */
    public function refresh(Token $token, RefreshRequest $request): JsonResponse
    {
        /**
         * @var \N1ebieski\ICore\Models\User
         */
        $user = $request->user();

        $user->currentAccessToken()->makeService()->delete();

        [$accessToken, $refreshToken] = $token->makeService()->create([
            'name' => 'login',
            'abilities' => ['api.*'],
            'expiration' => Config::get('sanctum.access_expiration'),
            'refresh' => true
        ]);

        return Response::json([
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken
        ], HttpResponse::HTTP_CREATED);
    }

    /**
     * Revoke token
     *
     * Revoke access token and refresh token.
     *
     * @authenticated
     *
     * @response 204 scenario=success
     *
     * @responseField access_token string
     * @responseField refresh_token string
     *
     * @param RevokeRequest $request
     * @return JsonResponse
     */
    public function revoke(RevokeRequest $request): JsonResponse
    {
        /**
         * @var \N1ebieski\ICore\Models\User
         */
        $user = $request->user();

        $user->currentAccessToken()->makeService()->delete();

        return Response::json('', HttpResponse::HTTP_NO_CONTENT);
    }
}
