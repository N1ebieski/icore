<?php

namespace N1ebieski\ICore\Repositories;

use Closure;
use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Config\Repository as Config;

/**
 * [PostRepo description]
 */
class PostRepo
{
    /**
     * [private description]
     * @var Post
     */
    protected $post;

    /**
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * Config
     * @var int
     */
    protected $paginate;

    /**
     * Undocumented function
     *
     * @param Post $post
     * @param Config $config
     * @param Carbon $carbon
     */
    public function __construct(Post $post, Config $config, Carbon $carbon)
    {
        $this->post = $post;

        $this->carbon = $carbon;

        $this->paginate = $config->get('database.paginate');
    }

    /**
     * Comments belong to the Post model
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateCommentsByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->post->comments()
            ->active()
            ->root()
            ->withAllRels($filter['orderby'])
            ->filterExcept($filter['except'])
            ->filterCommentsOrderBy($filter['orderby'])
            ->filterPaginate($this->paginate);
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->post->with('tags')
            ->filterExcept($filter['except'])
            ->filterSearch($filter['search'])
            ->filterStatus($filter['status'])
            ->filterOrderBy($filter['orderby'])
            ->filterCategory($filter['category'])
            ->filterPaginate($filter['paginate']);
    }

    /**
     * Route binding
     * @param  string $slug [description]
     * @return Post|null       [description]
     */
    public function firstBySlug(string $slug)
    {
        return $this->post->whereSlug($slug)
            ->active()
            ->with([
                'categories' => function ($query) {
                    $query->withAncestorsExceptSelf()->active();
                },
                'user:id,name',
                'tags'
            ])->first();
    }

    /**
     * [firstPrevious description]
     * @return Post|null [description]
     */
    public function firstPrevious()
    {
        return $this->post->active()
            ->where('id', '<', $this->post->id)
            ->orderBy('id', 'desc')
            ->first(['slug', 'title']);
    }

    /**
     * [firstNext description]
     * @return Post|null [description]
     */
    public function firstNext()
    {
        return $this->post->active()
            ->where('id', '>', $this->post->id)
            ->orderBy('id')
            ->first(['slug', 'title']);
    }

    /**
     * [getRelated description]
     * @param  int $limit [description]
     * @return Post|null         [description]
     */
    public function getRelated(int $limit = 5)
    {
        return $this->post->active()
            ->withAnyTags($this->post->tagList)
            ->where('posts.id', '<>', $this->post->id)
            ->limit($limit)
            ->inRandomOrder()
            ->get();
    }

    /**
     * [paginateArchiveByDate description]
     * @param  array                $date [description]
     * @return LengthAwarePaginator       [description]
     */
    public function paginateArchiveByDate(array $date) : LengthAwarePaginator
    {
        return $this->post->with('user:id,name')
            ->active()
            ->whereRaw(
                'MONTH(published_at) = ? and YEAR(published_at) = ?',
                [(int)$date['month'], (int)$date['year']]
            )
            ->orderBy('published_at', 'desc')
            ->paginate($this->paginate);
    }

    /**
     * [getArchives description]
     * @return Collection [description]
     */
    public function getArchives() : Collection
    {
        return $this->post->active()
            ->selectRaw('YEAR(published_at) year, MONTH(published_at) month, COUNT(*) posts_count')
            ->groupBy('year')
            ->groupBy('month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    /**
     * [paginateByTag description]
     * @param  string               $tag [description]
     * @return LengthAwarePaginator      [description]
     */
    public function paginateByTag(string $tag) : LengthAwarePaginator
    {
        return $this->post->withAllTags($tag)
            ->with('user:id,name')
            ->active()
            ->orderBy('published_at', 'desc')
            ->paginate($this->paginate);
    }

    /**
     * [paginateLatest description]
     * @return LengthAwarePaginator [description]
     */
    public function paginateLatest() : LengthAwarePaginator
    {
        return $this->post->with('user:id,name')
            ->active()
            ->orderBy('published_at', 'desc')
            ->paginate($this->paginate);
    }

    /**
     * [paginateBySearch description]
     * @param  string               $name [description]
     * @return LengthAwarePaginator       [description]
     */
    public function paginateBySearch(string $name) : LengthAwarePaginator
    {
        return $this->post->search($name)
            ->active()
            ->with('user:id,name')
            ->orderBy('published_at', 'desc')
            ->paginate($this->paginate);
    }

    /**
     * [updateActivateScheduled description]
     * @return bool              [description]
     */
    public function activateScheduled() : bool
    {
        return $this->post
            ->whereDate('published_at', '<', $this->carbon->now()->format('Y-m-d'))
            ->orWhere(function ($query) {
                $query->whereDate('published_at', '=', $this->carbon->now()->format('Y-m-d'))
                    ->whereTime('published_at', '<=', $this->carbon->now()->format('H:i:s'));
            })
            ->scheduled()
            ->update(['status' => Post::ACTIVE]);
    }

    /**
     * Undocumented function
     *
     * @param Closure $callback
     * @return boolean
     */
    public function chunkActiveWithModelsCount(Closure $callback) : bool
    {
        return $this->post->active()
            ->withCount(['comments AS models_count' => function ($query) {
                $query->root()->active();
            }])
            ->chunk(1000, $callback);
    }
}
