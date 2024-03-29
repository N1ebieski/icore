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

namespace N1ebieski\ICore\Http\Controllers\Web\Token;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
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
        [$newAccessToken, $newRefreshToken] = $token->makeService()->create($request->validated());

        $request->session()->flash('success', Lang::get('icore::tokens.success.store'));
        $request->session()->flash('accessToken', $newAccessToken->plainTextToken);

        return Response::json([
            'redirect' => URL::route('web.profile.tokens', [
                'filter' => [
                    'search' => "id:\"{$newAccessToken->accessToken->id}\""
                ]
            ])
        ], HttpResponse::HTTP_CREATED);
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
