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
use Illuminate\Auth\Access\HandlesAuthorization;
use N1ebieski\ICore\Models\Token\PersonalAccessToken as Token;

class TokenPolicy
{
    use HandlesAuthorization;

    /**
     * Undocumented function
     *
     * @param User $authUser
     * @param Token $token
     * @return bool
     */
    public function delete(User $authUser, Token $token): bool
    {
        return $authUser->can('admin.tokens.delete')
            || (
                $authUser->id === $token->tokenable_id
                && $token->tokenable_type === $authUser->getMorphClass()
            );
    }
}
