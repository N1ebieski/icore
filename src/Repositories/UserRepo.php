<?php

namespace N1ebieski\ICore\Repositories;

use N1ebieski\ICore\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * [UserRepo description]
 */
class UserRepo
{
    /**
     * [private description]
     * @var User
     */
    protected $user;

    /**
     * [__construct description]
     * @param User $user [description]
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->user->filterSearch($filter['search'])
            ->filterStatus($filter['status'])
            ->filterRole($filter['role'])
            ->filterOrderBy($filter['orderby'])
            ->with(['roles', 'socialites'])
            ->filterPaginate($filter['paginate']);
    }

    /**
     * [firstByEmail description]
     * @param  string $email [description]
     * @return User|null        [description]
     */
    public function firstByEmail(string $email) : ?User
    {
        return $this->user->where('email', $email)->first();
    }
}
