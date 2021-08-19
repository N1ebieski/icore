<?php

namespace N1ebieski\ICore\Http\Resources\Socialite;

use N1ebieski\ICore\Models\Socialite;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialiteResource extends JsonResource
{
    /**
     * Undocumented function
     *
     * @param Socialite $socialite
     */
    public function __construct(Socialite $socialite)
    {
        parent::__construct($socialite);
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
            'provider_name' => $this->provider_name,
            'created_at' => $this->created_at,
            'created_at_diff' => $this->created_at_diff,
            'updated_at' => $this->when(
                optional($request->user())->can('admin.users.view'),
                function () {
                    return $this->updated_at;
                }
            ),
            'updated_at_diff' => $this->when(
                optional($request->user())->can('admin.users.view'),
                function () {
                    return $this->updated_at_diff;
                }
            )
        ];
    }
}
