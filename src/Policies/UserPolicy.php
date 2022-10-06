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

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Undocumented function
     *
     * @param User $authUser
     * @param User $user
     * @return bool
     */
    public function view(User $authUser, User $user): bool
    {
        return $authUser->can('admin.users.view')
            || $authUser->id === $user->id;
    }

    /**
     * Undocumented function
     *
     * @param User $authUser
     * @param User $user
     * @return bool
     */
    public function actionSelf(User $authUser, User $user): bool
    {
        return $authUser->id !== $user->id;
    }

    /**
     * Undocumented function
     *
     * @param User $authUser
     * @param array $ids
     * @return boolean
     */
    public function deleteGlobalSelf(User $authUser, array $ids): bool
    {
        foreach ($ids as $id) {
            if ($authUser->id === (int)$id) {
                return false;
            }
        }

        return true;
    }
}
