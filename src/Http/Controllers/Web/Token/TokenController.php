<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Token;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\Services\TokenService;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Http\Requests\Web\Token\StoreRequest;
use N1ebieski\ICore\Models\Token\PersonalAccessToken as Token;

class TokenController
{
    /**
     * Undocumented variable
     *
     * @var TokenService
     */
    protected $tokenService;

    /**
     * Undocumented function
     *
     * @param TokenService $tokenService
     */
    public function __construct(TokenService $tokenService)
    {

        $this->tokenService = $tokenService;
    }

    /**
     * Undocumented function
     *
     * @param Token $token
     * @return JsonResponse
     */
    public function create(Token $token): JsonResponse
    {
        $abilities = Collect::make($token::ABILITIES);

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
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['expiration'] = $validated['expiration'] !== null ?
            (int)$validated['expiration'] * 24 * 60
            : null;

        [$accessToken, $refreshToken] = $this->tokenService->create($validated);

        $request->session()->flash('success', Lang::get('icore::tokens.success.store'));
        $request->session()->flash('accessToken', $accessToken->plainTextToken);

        return Response::json([], HttpResponse::HTTP_NO_CONTENT);
    }
}
