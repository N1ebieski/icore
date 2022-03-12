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
            'updated_at' => $this->updated_at,
        ];
    }
}
