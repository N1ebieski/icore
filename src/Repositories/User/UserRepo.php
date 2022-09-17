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

namespace N1ebieski\ICore\Repositories\User;

use N1ebieski\ICore\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\Guard as Auth;
use N1ebieski\ICore\Models\Token\PersonalAccessToken;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepo
{
    /**
     * Undocumented function
     *
     * @param User $user
     * @param Auth $auth
     */
    public function __construct(
        protected User $user,
        protected Auth $auth
    ) {
        //
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
    {
        return $this->user->newQuery()
            ->selectRaw("`{$this->user->getTable()}`.*")
            ->when(
                is_null($filter['status']) && !$this->auth->user()?->can('admin.users.view'),
                function (Builder|User $query) {
                    return $query->active();
                },
                function (Builder|User $query) use ($filter) {
                    return $query->filterStatus($filter['status']);
                }
            )
            ->filterSearch($filter['search'])
            ->filterExcept($filter['except'])
            ->filterRole($filter['role'])
            ->when(is_null($filter['orderby']), function (Builder|User $query) use ($filter) {
                return $query->filterOrderBySearch($filter['search']);
            })
            ->filterOrderBy($filter['orderby'])
            ->with(['roles', 'socialites'])
            ->filterPaginate($filter['paginate']);
    }

    /**
     * [firstByEmail description]
     * @param  string $email [description]
     * @return User|null        [description]
     */
    public function firstByEmail(string $email): ?User
    {
        return $this->user->where('email', $email)->first();
    }

    /**
     * Undocumented function
     *
     * @param array $filter
     * @return LengthAwarePaginator
     */
    public function paginateTokensByFilter(array $filter): LengthAwarePaginator
    {
        /** @var Builder|PersonalAccessToken */
        $tokens = $this->user->tokens();

        /**
         * @var PersonalAccessToken $token
         */
        $token = $tokens->make();

        // @phpstan-ignore-next-line
        return $tokens->selectRaw("`{$token->getTable()}`.*")
            ->whereJsonDoesntContain('abilities', 'refresh')
            ->filterExcept($filter['except'])
            ->filterSearch($filter['search'])
            ->filterStatus($filter['status'])
            ->when($filter['orderby'] === null, function (Builder|PersonalAccessToken $query) use ($filter) {
                return $query->filterOrderBySearch($filter['search']);
            })
            ->filterOrderBy($filter['orderby'])
            ->filterPaginate($filter['paginate']);
    }
}
