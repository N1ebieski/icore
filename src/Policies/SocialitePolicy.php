<?php

namespace N1ebieski\ICore\Policies;

use N1ebieski\ICore\Models\Socialite;
use N1ebieski\ICore\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SocialitePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function delete(User $current_user, Socialite $socialite)
    {
        return $current_user->id === $socialite->user_id;
    }
}
