<?php

namespace N1ebieski\ICore\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use N1ebieski\ICore\Models\Report\Report;

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
        return $query->when($search !== null, function ($query) use ($search) {
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
        return $query->when($status !== null, function ($query) use ($status) {
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
     * Undocumented function
     *
     * @param Builder $query
     * @param string $search
     * @param string $orderby
     * @return Builder
     */
    public function scopeFilterOrderBySearch(Builder $query, string $search = null) : Builder
    {
        return $query->when($search !== null, function ($query) use ($search) {
            return $query->orderBySearch($search);
        });
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
            return $query->orderBy($order[0] ?: 'updated_at', $order[1] ?: 'desc');
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
        return $query->when($report === Report::REPORTED, function ($query) {
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
        return $query->when($author !== null, function ($query) use ($author) {
            return $query->where('user_id', $author->id);
        });
    }

    /**
     * [scopeFilterCategory description]
     * @param  Builder $query    [description]
     * @param  Category|null  $category [description]
     * @return Builder|null            [description]
     */
    public function scopeFilterCategory(Builder $query, Category $category = null) : ?Builder
    {
        return $query->when($category !== null, function ($query) use ($category) {
            $query->whereHas('categories', function ($q) use ($category) {
                return $q->where('category_id', $category->id);
            });
        });
    }

    /**
     * [scopeFilterExcept description]
     * @param  Builder $query  [description]
     * @param  array|null  $except [description]
     * @return Builder|null         [description]
     */
    public function scopeFilterExcept(Builder $query, array $except = null)
    {
        return $query->when($except !== null, function ($query) use ($except) {
            $query->whereNotIn("{$this->getTable()}.id", $except);
        });
    }
}
