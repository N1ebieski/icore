<?php

namespace N1ebieski\ICore\Repositories\Mailing;

use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Mailing;
use N1ebieski\ICore\ValueObjects\Mailing\Status;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MailingRepo
{
    /**
     * [private description]
     * @var Mailing
     */
    protected $mailing;

    /**
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * Undocumented function
     *
     * @param Mailing $mailing
     * @param Carbon $carbon
     */
    public function __construct(Mailing $mailing, Carbon $carbon)
    {
        $this->mailing = $mailing;

        $this->carbon = $carbon;
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
    {
        return $this->mailing->filterSearch($filter['search'])
            ->filterExcept($filter['except'])
            ->filterStatus($filter['status'])
            ->when($filter['orderby'] === null, function ($query) use ($filter) {
                $query->filterOrderBySearch($filter['search']);
            })
            ->filterOrderBy($filter['orderby'])
            ->with('emails')
            ->filterPaginate($filter['paginate']);
    }

    /**
     * [activateScheduled description]
     * @return bool              [description]
     */
    public function activateScheduled(): bool
    {
        return $this->mailing
            ->whereDate('activation_at', '<', $this->carbon->now()->format('Y-m-d'))
            ->orWhere(function ($query) {
                $query->whereDate('activation_at', '=', $this->carbon->now()->format('Y-m-d'))
                    ->whereTime('activation_at', '<=', $this->carbon->now()->format('H:i:s'));
            })
            ->scheduled()
            ->update(['status' => Status::ACTIVE]);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function deactivateCompleted(): bool
    {
        return $this->mailing->progress()
            ->whereDoesntHave('emails', function ($query) {
                $query->unsent();
            })
            ->update([
                'status' => Status::INACTIVE,
                'activation_at' => null
            ]);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function progressActivated(): bool
    {
        return $this->mailing->active()
            ->whereHas('emails', function ($query) {
                $query->unsent();
            })
            ->update(['status' => Status::INPROGRESS]);
    }
}
