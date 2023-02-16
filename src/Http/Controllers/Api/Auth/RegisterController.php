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

namespace N1ebieski\ICore\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Http\Controllers\Auth\RegisterController as BaseRegisterController;

/**
 * @group Authentication
 */
class RegisterController
{
    /**
     * Undocumented function
     *
     * @param BaseRegisterController $decorated
     */
    public function __construct(protected BaseRegisterController $decorated)
    {
        //
    }

    /**
     * Register
     *
     * Create user and send email verification notification
     *
     * @unauthenticated
     *
     * @bodyParam name string required Example: N1ebieski
     * @bodyParam email string required Example: example@example.pl
     * @bodyParam password string required Example: demo1234
     * @bodyParam password_confirmation string required Example: demo1234
     * @bodyParam privacy_agreement boolean required Acceptance of privacy policy Example: true
     * @bodyParam contact_agreement boolean required Acceptance of receiving e-mail system notifications Example: true
     * @bodyParam marketing_agreement boolean Acceptance of receiving marketing information notifications Example: true
     *
     * @apiResource N1ebieski\ICore\Http\Resources\User\UserResource
     * @apiResourceModel N1ebieski\ICore\Models\User states=active,user with=roles
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $this->decorated->register($request);

        /**
         * @var \N1ebieski\ICore\Models\User
         */
        $user = $request->user();

        Auth::logout();

        return $user->makeResource()
            ->response()
            ->setStatusCode(HttpResponse::HTTP_CREATED);
    }
}
