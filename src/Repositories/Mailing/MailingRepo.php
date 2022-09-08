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

namespace N1ebieski\ICore\Repositories\Mailing;

use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Mailing;
use N1ebieski\ICore\ValueObjects\Mailing\Status;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MailingRepo
{
    /**
     * Undocumented function
     *
     * @param Mailing $mailing
     * @param Carbon $carbon
     */
    public function __construct(
        protected Mailing $mailing,
        protected Carbon $carbon
    ) {
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
        return $this->mailing->selectRaw("`{$this->mailing->getTable()}`.*")
            ->filterSearch($filter['search'])
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
