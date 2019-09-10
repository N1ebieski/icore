<?php

namespace N1ebieski\ICore\Policies;

use N1ebieski\ICore\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function actionSelf(User $current_user, User $user)
    {
        return $current_user->id !== $user->id;
    }

    public function deleteGlobalSelf(User $current_user, array $ids) : bool
    {
        foreach ($ids as $id) {
            if ($current_user->id === (int)$id) return false;
        }

        return true;
    }
}
