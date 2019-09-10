<?php

namespace N1ebieski\ICore\Repositories;

use N1ebieski\ICore\Models\Socialite;

/**
 * [SocialiteRepo description]
 */
class SocialiteRepo
{
    /**
     * [private description]
     * @var Socialite
     */
    private $socialite;

    /**
     * [__construct description]
     * @param Socialite $socialite [description]
     */
    public function __construct(Socialite $socialite)
    {
        $this->socialite = $socialite;
    }

    /**
     * [firstByProvider description]
     * @param  string $name [description]
     * @param  string $id   [description]
     * @return Socialite|null       [description]
     */
    public function firstByProvider(string $name, string $id) : ?Socialite
    {
        return $this->socialite->where('provider_name', $name)
            ->where('provider_id', $id)->first();
    }
}
