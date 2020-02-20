<?php

namespace N1ebieski\ICore\Repositories;

use N1ebieski\ICore\Models\Mailing;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

/**
 * [MailingRepo description]
 */
class MailingRepo
{
    /**
     * [private description]
     * @var Mailing
     */
    protected $mailing;

    /**
     * [__construct description]
     * @param Mailing $mailing [description]
     */
    public function __construct(Mailing $mailing)
    {
        $this->mailing = $mailing;
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->mailing->filterSearch($filter['search'])
            ->filterExcept($filter['except'])
            ->filterStatus($filter['status'])
            ->filterOrderBy($filter['orderby'])
            ->with('emails')
            ->filterPaginate($filter['paginate']);
    }

    /**
     * [activateScheduled description]
     * @return bool              [description]
     */
    public function activateScheduled() : bool
    {
        return $this->mailing
            ->whereDate('activation_at', '<', Carbon::now()->format('Y-m-d'))
            ->orWhere(function ($query) {
                $query->whereDate('activation_at', '=', Carbon::now()->format('Y-m-d'))
                    ->whereTime('activation_at', '<=', Carbon::now()->format('H:i:s'));
            })
            ->whereStatus(2)
            ->update(['status' => 1]);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function deactivateCompleted() : bool
    {
        return $this->mailing->active()
            ->whereDoesntHave('emails', function ($query) {
                $query->where('status', 0);
            })
            ->update([
                'status' => 0,
                'activation_at' => null
            ]);
    }
}
