<?php

namespace N1ebieski\ICore\Traits;

use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * [trait description]
 */
trait Filterable
{
    /**
     * [scopeFilterSearch description]
     * @param  Builder $query  [description]
     * @param  string|null  $search [description]
     * @return Builder|null          [description]
     */
    public function scopeFilterSearch(Builder $query, string $search = null) : ?Builder
    {
        return $query->when($search !== null, function($query) use ($search) {
            return $query->search($search);
        });
    }

    /**
     * [scopeFilterStatus description]
     * @param  Builder $query  [description]
     * @param  int|null  $status [description]
     * @return Builder|null          [description]
     */
    public function scopeFilterStatus(Builder $query, int $status = null) : ?Builder
    {
        return $query->when($status !== null, function($query) use ($status) {
            return $query->where('status', $status);
        });
    }

    /**
     * [scopeFilterPaginate description]
     * @param  Builder $query    [description]
     * @param  int|null  $paginate [description]
     * @return LengthAwarePaginator [description]
     */
    public function scopeFilterPaginate(Builder $query, int $paginate = null) : LengthAwarePaginator
    {
        return $query->paginate($paginate ?? config('database.paginate'));
    }

    /**
     * [scopeFilterOrderBy description]
     * @param  Builder $query   [description]
     * @param  string|null  $orderby [description]
     * @return Builder           [description]
     */
    public function scopeFilterOrderBy(Builder $query, string $orderby = null) : Builder
    {
        $order = explode('|', $orderby);

        if (count($order) == 2) {
            return $query->orderBy($order[0] ?: 'updated_at', $order[1] ?: 'desc')
                ->orderBy('id', 'asc');
        }

        return $query->latest();
    }

    /**
     * [scopeFilterReport description]
     * @param  Builder $query  [description]
     * @param  int|null $report [description]
     * @return Builder|null          [description]
     */
    public function scopeFilterReport(Builder $query, int $report = null) : ?Builder
    {
        return $query->when($report === 1, function($query) use ($report) {
            return $query->whereHas('reports');
        });
    }

    /**
     * [scopeFilterAuthor description]
     * @param  Builder $query  [description]
     * @param  User|null  $author [description]
     * @return Builder|null          [description]
     */
    public function scopeFilterAuthor(Builder $query, User $author = null) : ?Builder
    {
        return $query->when($author !== null, function($query) use ($author) {
            return $query->where('user_id', $author->id);
        });
    }
}
