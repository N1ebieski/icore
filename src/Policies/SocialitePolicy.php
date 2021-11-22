<?php

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
