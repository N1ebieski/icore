<?php

namespace N1ebieski\ICore\Repositories;

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
    private $comment;

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
            ->with('morph:id,title,comment')
            ->with('user:id,name')
            ->withCount('reports')
            ->filterSearch($filter['search'])
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

    public function countInactive()
    {
        return $this->comment->poliType()->inactive()->count();
    }

    public function countInactiveByModelType()
    {
        return $this->comment->inactive()
            ->selectRaw('TRIM(LOWER(SUBSTRING_INDEX(model_type, "\\\", -1))) AS `model`, COUNT(*) AS `count`')
            ->groupBy('model_type')
            ->get();
    }
}
