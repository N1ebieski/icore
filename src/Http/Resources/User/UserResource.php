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

namespace N1ebieski\ICore\Http\Resources\User;

use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Resources\Json\JsonResource;
use N1ebieski\ICore\Http\Resources\Role\RoleResource;
use N1ebieski\ICore\Http\Resources\Socialite\SocialiteResource;

/**
 * @mixin User
 * @property int|null $depth
 */
class UserResource extends JsonResource
{
    /**
     * Undocumented function
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * Transform the resource into an array.
     *
     * @responseField id int
     * @responseField name string
     * @responseField ip string (available only for admin.users.view).
     * @responseField email string (available only for admin.users.view or owner).
     * @responseField status object Contains int value and string label
     * @responseField marketing object Email marketing consent, contains int value and string label (available only for admin.users.view or owner).
     * @responseField created_at string
     * @responseField updated_at string
     * @responseField roles object[] Contains relationships Roles.
     * @responseField socialites object[] Contains relationships Socialites (available only for admin.users.view or owner).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            $this->mergeWhen(
                optional($request->user())->can('admin.users.view'),
                function () {
                    return [
                        'ip' => $this->ip
                    ];
                }
            ),
            $this->mergeWhen(
                optional($request->user())->can('view', $this->resource),
                function () {
                    return [
                        'email' => $this->email
                    ];
                }
            ),
            'status' => [
                'value' => $this->status->getValue(),
                'label' => Lang::get("icore::users.status.{$this->status}")
            ],
            $this->mergeWhen(
                optional($request->user())->can('view', $this->resource),
                function () {
                    return [
                        'marketing' => $this->marketing->getValue()
                    ];
                }
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'roles' => App::make(RoleResource::class)->collection($this->whenLoaded('roles')),
            $this->mergeWhen(
                $this->depth === null,
                function () use ($request) {
                    return [
                        'socialites' => $this->when(
                            optional($request->user())->can('view', $this->resource),
                            function () {
                                return  App::make(SocialiteResource::class)->collection($this->whenLoaded('socialites'));
                            }
                        )
                    ];
                }
            )
        ];
    }
}
