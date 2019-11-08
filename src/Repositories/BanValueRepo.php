<?php

namespace N1ebieski\ICore\Repositories;

use N1ebieski\ICore\Models\BanValue;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * [BanValueRepo description]
 */
class BanValueRepo
{
    /**
     * [private description]
     * @var BanValue
     */
    protected $banValue;

    /**
     * [__construct description]
     * @param BanValue $banValue [description]
     */
    public function __construct(BanValue $banValue)
    {
        $this->banValue = $banValue;
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->banValue->filterType($filter['type'])
            ->filterSearch($filter['search'])
            ->filterOrderBy($filter['orderby'])
            ->filterPaginate($filter['paginate']);
    }
}
