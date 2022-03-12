<?php

namespace N1ebieski\ICore\Http\Resources\User;

use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Resources\Json\JsonResource;
use N1ebieski\ICore\Http\Resources\Role\RoleResource;
use N1ebieski\ICore\Http\Resources\Socialite\SocialiteResource;

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
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            $this->mergeWhen(
                $this->depth === 0,
                function () use ($request) {
                    return [
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
                            'value' => $this->status,
                            'label' => Lang::get("icore::users.status.{$this->status}")
                        ],
                        $this->mergeWhen(
                            optional($request->user())->can('view', $this->resource),
                            function () {
                                return [
                                    'marketing' => $this->marketing
                                ];
                            }
                        ),
                        'created_at' => $this->created_at,
                        'created_at_diff' => $this->created_at_diff,
                        'updated_at' => $this->updated_at,
                        'updated_at_diff' => $this->updated_at_diff
                    ];
                }
            ),
            'roles' => App::make(RoleResource::class)->collection($this->whenLoaded('roles')),
            $this->mergeWhen(
                $this->depth === 0,
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
