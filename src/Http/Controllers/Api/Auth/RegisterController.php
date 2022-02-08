<?php

namespace N1ebieski\ICore\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Http\Resources\User\UserResource;
use N1ebieski\ICore\Http\Controllers\Auth\LoginController;
use N1ebieski\ICore\Http\Controllers\Auth\RegisterController as BaseRegisterController;

/**
 * @group Authentication
 */
class RegisterController
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
     * @param BaseRegisterController $decorated
     */
    public function __construct(BaseRegisterController $decorated)
    {
        $this->decorated = $decorated;
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
     * @responseField id int
     * @responseField name string
     * @responseField ip string (available only for admin.users.view).
     * @responseField email string (available only for admin.users.view or owner).
     * @responseField status object Contains int value and string label
     * @responseField marketing object Email marketing consent, contains int value and string label (available only for admin.users.view or owner).
     * @responseField created_at string
     * @responseField created_at_diff string
     * @responseField updated_at string
     * @responseField updated_at_diff string
     * @responseField roles object[] Contains relationships Roles.
     * @responseField socialites object[] Contains relationships Socialites (available only for admin.users.view or owner).
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

        return App::make(UserResource::class, ['user' => Auth::user()])
            ->response()
            ->setStatusCode(HttpResponse::HTTP_CREATED);
    }
}
