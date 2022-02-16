<?php

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
