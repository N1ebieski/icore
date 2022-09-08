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
use Illuminate\Contracts\Auth\Factory as Auth;
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
        return $this->user->selectRaw("`{$this->user->getTable()}`.*")
            ->filterSearch($filter['search'])
            ->filterExcept($filter['except'])
            ->when(
                $filter['status'] === null && !optional($this->auth->user())->can('admin.users.view'),
                function ($query) {
                    $query->active();
                },
                function ($query) use ($filter) {
                    $query->filterStatus($filter['status']);
                }
            )
            ->filterRole($filter['role'])
            ->when($filter['orderby'] === null, function ($query) use ($filter) {
                $query->filterOrderBySearch($filter['search']);
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
        /**
         * @var \N1ebieski\ICore\Models\Token\PersonalAccessToken $token
         */
        $token = $this->user->tokens()->make();

        return $this->user->tokens()
            ->selectRaw("`{$token->getTable()}`.*")
            ->whereJsonDoesntContain('abilities', 'refresh')
            ->filterExcept($filter['except'])
            ->filterSearch($filter['search'])
            ->filterStatus($filter['status'])
            ->when($filter['orderby'] === null, function ($query) use ($filter) {
                $query->filterOrderBySearch($filter['search']);
            })
            ->filterOrderBy($filter['orderby'])
            ->filterPaginate($filter['paginate']);
    }
}
