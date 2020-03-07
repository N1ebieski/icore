<?php

namespace N1ebieski\ICore\Policies;

use N1ebieski\ICore\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Undocumented function
     *
     * @param User $current_user
     * @param User $user
     * @return void
     */
    public function actionSelf(User $current_user, User $user)
    {
        return $current_user->id !== $user->id;
    }

    /**
     * Undocumented function
     *
     * @param User $current_user
     * @param array $ids
     * @return boolean
     */
    public function deleteGlobalSelf(User $current_user, array $ids) : bool
    {
        foreach ($ids as $id) {
            if ($current_user->id === (int)$id) {
                return false;
            }
        }

        return true;
    }
}
