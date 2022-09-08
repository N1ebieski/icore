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

namespace N1ebieski\ICore\Policies;

use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Socialite;
use Illuminate\Auth\Access\HandlesAuthorization;

class SocialitePolicy
{
    use HandlesAuthorization;

    /**
     * Undocumented function
     *
     * @param User $current_user
     * @param Socialite $socialite
     * @return void
     */
    public function delete(User $current_user, Socialite $socialite)
    {
        return $current_user->id === $socialite->user_id;
    }
}
