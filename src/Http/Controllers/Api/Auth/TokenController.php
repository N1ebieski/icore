<?php

namespace N1ebieski\ICore\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\Services\TokenService;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Http\Controllers\Auth\LoginController;

class TokenController
{
    /**
     * Undocumented variable
     *
     * @var LoginController
     */
    protected $decorated;

    /**
     * Undocumented variable
     *
     * @var TokenService
     */
    protected $tokenService;

    /**
     * Undocumented function
     *
     * @param LoginController $decorated
     * @param TokenService $tokenService
     */
    public function __construct(LoginController $decorated, TokenService $tokenService)
    {
        $this->decorated = $decorated;

        $this->tokenService = $tokenService;
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function token(Request $request): JsonResponse
    {
        // API is stateless but we use validate login logic from laravel/ui
        $request->setLaravelSession(optional());

        $this->decorated->login($request);

        [$accessToken, $refreshToken] = $this->tokenService->create([
            'name' => 'login',
            'scopes' => ['api.*'],
            'expiration' => Config::get('sanctum.access_expiration'),
            'refresh' => filter_var($request->input('remember'), FILTER_VALIDATE_BOOLEAN) ?: null
        ]);

        return Response::json(array_filter([
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => isset($refreshToken) ? $refreshToken->plainTextToken : null
        ]));
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        /**
         * @var \N1ebieski\ICore\Models\User
         */
        $user = $request->user();

        $this->tokenService->setToken($user->currentAccessToken())->delete();

        [$accessToken, $refreshToken] = $this->tokenService->create([
            'name' => 'login',
            'scopes' => ['api.*'],
            'expiration' => Config::get('sanctum.access_expiration'),
            'refresh' => true
        ]);

        return Response::json([
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function revoke(Request $request): JsonResponse
    {
       /**
         * @var \N1ebieski\ICore\Models\User
         */
        $user = $request->user();

        $this->tokenService->setToken($user->currentAccessToken())->delete();

        return Response::json('', HttpResponse::HTTP_NO_CONTENT);
    }
}
