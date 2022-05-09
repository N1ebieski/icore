<?php

namespace N1ebieski\ICore\Repositories\User;

use N1ebieski\ICore\Models\User;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepo
{
    /**
     * [private description]
     * @var User
     */
    protected $user;

    /**
     * Undocumented variable
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Undocumented function
     *
     * @param User $user
     * @param Auth $auth
     */
    public function __construct(User $user, Auth $auth)
    {
        $this->user = $user;

        $this->auth = $auth;
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
    {
        return $this->user->filterSearch($filter['search'])
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
        return $this->user->tokens()
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
