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
     * @param User $authUser
     * @param User $user
     * @return void
     */
    public function view(User $authUser, User $user)
    {
        return $authUser->can('admin.users.view')
            || $authUser->id === $user->id;
    }

    /**
     * Undocumented function
     *
     * @param User $authUser
     * @param User $user
     * @return void
     */
    public function actionSelf(User $authUser, User $user)
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
