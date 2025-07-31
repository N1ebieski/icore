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

namespace N1ebieski\ICore\Repositories\Comment;

use N1ebieski\ICore\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use N1ebieski\ICore\Models\Rating\Rating;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CommentRepo
{
    /**
     * [__construct description]
     * @param Comment $comment [description]
     */
    public function __construct(protected Comment $comment)
    {
        //
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
    {
        return $this->comment->newQuery()
            ->selectRaw("`{$this->comment->getTable()}`.*")
            ->when(!is_null($filter['search']), function (Builder|Comment $query) use ($filter) {
                return $query->filterSearch($filter['search'])
                    ->when(array_key_exists('user', $this->comment->search), function (Builder $query) {
                        $user = $this->comment->user()->make();

                        $columns = implode(',', $user->searchable);

                        return $query->leftJoin('users', function (JoinClause $query) {
                            return $query->on('users.id', '=', 'comments.user_id');
                        })
                        ->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", [
                            $this->comment->search['user']
                        ]);
                    });
            })
            ->poliType()
            ->filterExcept($filter['except'])
            ->filterStatus($filter['status'])
            ->filterCensored($filter['censored'])
            ->filterReport($filter['report'])
            ->filterAuthor($filter['author'])
            ->when(is_null($filter['orderby']), function ($query) use ($filter) {
                return $query->filterOrderBySearch($filter['search']);
            })
            ->filterOrderBy($filter['orderby'])
            ->with(['morph', 'user'])
            ->withCount('reports')
            ->filterPaginate($filter['paginate']);
    }

    /**
     * [getAncestorsAsArray description]
     * @return array [description]
     */
    public function getAncestorsAsArray(): array
    {
        return $this->comment->ancestors()->pluck('id')->toArray();
    }

    /**
     * [getDescendantsAsArray description]
     * @return array [description]
     */
    public function getDescendantsAsArray(): array
    {
        return $this->comment->descendants()->pluck('id')->toArray();
    }

    /**
     * [paginateChildrensByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateChildrensByFilter(array $filter): LengthAwarePaginator
    {
        /** @var Comment */
        $childrens = $this->comment->childrens();

        // @phpstan-ignore-next-line
        return $childrens->active()
            ->withAllRels($filter['orderby'])
            ->filterExcept($filter['except'])
            ->filterCommentsOrderBy($filter['orderby'])
            ->filterPaginate(5);
    }

    /**
     *
     * @param User $user
     * @return null|Rating
     */
    public function firstRatingByUser(User $user): ?Rating
    {
        return $this->comment->ratings()->where('user_id', $user->id)->first();
    }

    /**
     * [countByModelType description]
     * @return Collection [description]
     */
    public function countByModelTypeAndStatus(): Collection
    {
        return $this->comment
            ->selectRaw('TRIM(LOWER(SUBSTRING_INDEX(model_type, "\\\", -1))) AS `model`, `status`, COUNT(*) AS `count`')
            ->groupBy('model_type', 'status')
            ->get();
    }

    /**
     * [countReportedByModelType description]
     * @return Collection [description]
     */
    public function countReportedByModelType(): Collection
    {
        return $this->comment->whereHas('reports')
            ->selectRaw('TRIM(LOWER(SUBSTRING_INDEX(model_type, "\\\", -1))) AS `model`, COUNT(*) AS `count`')
            ->groupBy('model_type')
            ->get();
    }

    /**
     * Undocumented function
     *
     * @param array $component
     * @return Collect
     */
    public function getByComponent(array $component): Collect
    {
        return $this->comment->newQuery()
            ->active()
            ->uncensored()
            ->whereHasMorph('morph', [$this->comment->model_type], function ($query) {
                return $query->active();
            })
            ->when($component['orderby'] === 'rand', function (Builder|Comment $query) {
                return $query->inRandomOrder();
            }, function (Builder|Comment $query) use ($component) {
                return $query->filterOrderBy($component['orderby']);
            })
            ->limit($component['limit'])
            ->with(['morph', 'user'])
            ->get()
            ->map(function (Comment $comment) use ($component) {
                if ($component['max_content'] !== null) {
                    $comment->content = mb_substr($comment->content, 0, $component['max_content']) . '...';
                }

                return $comment;
            });
    }
}
