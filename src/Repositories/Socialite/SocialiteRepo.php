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

namespace N1ebieski\ICore\Repositories\Socialite;

use N1ebieski\ICore\Models\Socialite;

class SocialiteRepo
{
    /**
     * [__construct description]
     * @param Socialite $socialite [description]
     */
    public function __construct(protected Socialite $socialite)
    {
        //
    }

    /**
     * [firstByProvider description]
     * @param  string $name [description]
     * @param  string $id   [description]
     * @return Socialite|null       [description]
     */
    public function firstByProvider(string $name, string $id): ?Socialite
    {
        return $this->socialite->where('provider_name', $name)
            ->where('provider_id', $id)
            ->first();
    }
}
