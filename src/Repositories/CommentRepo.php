<?php

namespace N1ebieski\ICore\Repositories;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Models\Rating\Rating;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * [CommentRepo description]
 */
class CommentRepo
{
    /**
     * [private description]
     * @var Comment
     */
    protected $comment;

    /**
     * [__construct description]
     * @param Comment $comment [description]
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->comment->poliType()
            ->with(['morph', 'user'])
            ->withCount('reports')
            ->filterExcept($filter['except'])
            ->when($filter['search'] !== null, function ($query) use ($filter) {
                $query->filterSearch($filter['search'])
                    ->when(array_key_exists('user', $this->comment->search), function ($query) {
                        $user = $this->comment->user()->make();

                        $columns = implode(',', $user->searchable);

                        $query->leftJoin('users', function ($query) {
                            $query->on('users.id', '=', 'comments.user_id');
                        })
                        ->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", [
                            $this->comment->search['user']
                        ]);
                    });
            })
            ->filterStatus($filter['status'])
            ->filterCensored($filter['censored'])
            ->filterReport($filter['report'])
            ->filterAuthor($filter['author'])
            ->filterOrderBy($filter['orderby'])
            ->filterPaginate($filter['paginate']);
    }

    /**
     * [getAncestorsAsArray description]
     * @return array [description]
     */
    public function getAncestorsAsArray() : array
    {
        return $this->comment->ancestors()->get(['id'])->pluck('id')->toArray();
    }

    /**
     * [getDescendantsAsArray description]
     * @return array [description]
     */
    public function getDescendantsAsArray() : array
    {
        return $this->comment->descendants()->get(['id'])->pluck('id')->toArray();
    }

    /**
     * [paginateChildrensByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateChildrensByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->comment->childrens()
            ->active()
            ->withAllRels($filter['orderby'])
            // Filtrujemy wcześniejsze komentarze, aby na froncie nie pojawiły się duplikaty
            ->filterExcept($filter['except'])
            ->filterCommentsOrderBy($filter['orderby'])
            ->filterPaginate(5);
    }

    /**
     * [firstRatingByUser description]
     * @param  int    $id [description]
     * @return Rating|null     [description]
     */
    public function firstRatingByUser(int $id) : ?Rating
    {
        return $this->comment->ratings()->where('user_id', $id)->first();
    }

    /**
     * [countByModelType description]
     * @return Collection [description]
     */
    public function countByModelTypeAndStatus() : Collection
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
    public function countReportedByModelType() : Collection
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
     * @return Collection
     */
    public function getLatestByComponent(array $component) : Collection
    {
        return $this->comment->active()
            ->uncensored()
            ->whereHasMorph('morph', [$this->comment->model_type], function ($query) {
                $query->active();
            })
            ->with(['morph', 'user'])
            ->latest()
            ->limit($component['limit'])
            ->get();
    }
}
