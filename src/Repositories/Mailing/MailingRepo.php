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

use N1ebieski\ICore\Models\Mailing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\Guard as Auth;
use N1ebieski\ICore\ValueObjects\MailingEmail\Sent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MailingRepo
{
    /**
     *
     * @param Mailing $mailing
     * @param Auth $auth
     * @return void
     */
    public function __construct(
        protected Mailing $mailing,
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
        return $this->mailing->newQuery()
            ->selectRaw("`{$this->mailing->getTable()}`.*")
            ->filterExcept($filter['except'])
            ->filterStatus($filter['status'])
            ->when(!is_null($filter['search']), function (Builder|Mailing $query) use ($filter) {
                return $query->filterSearch($filter['search'])
                    ->when($this->auth->user()?->can('admin.mailings.view'), function (Builder $query) {
                        return $query->where(function (Builder $query) {
                            foreach (['id'] as $attr) {
                                return $query->when(array_key_exists($attr, $this->mailing->search), function (Builder $query) use ($attr) {
                                    return $query->where("{$this->mailing->getTable()}.{$attr}", $this->mailing->search[$attr]);
                                });
                            }
                        });
                    });
            })
            ->when(is_null($filter['orderby']), function (Builder|Mailing $query) use ($filter) {
                return $query->filterOrderBySearch($filter['search']);
            })
            ->filterOrderBy($filter['orderby'])
            ->withCount([
                'emails',
                'emails AS emails_success_count' => function (Builder $query) {
                    return $query->where('sent', Sent::SENT);
                },
                'emails AS emails_failed_count' => function (Builder $query) {
                    return $query->where('sent', Sent::ERROR);
                }
            ])
            ->filterPaginate($filter['paginate']);
    }
}
