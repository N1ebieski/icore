<?php

namespace N1ebieski\ICore\Services;

use Carbon\Carbon;
use N1ebieski\ICore\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Updatable;
use N1ebieski\ICore\Services\Interfaces\FullUpdatable;
use N1ebieski\ICore\Services\Interfaces\StatusUpdatable;
use N1ebieski\ICore\Services\Interfaces\Deletable;
use N1ebieski\ICore\Services\Interfaces\GlobalDeletable;

/**
 * [PostService description]
 */
class PostService implements
    Creatable,
    Updatable,
    FullUpdatable,
    StatusUpdatable,
    Deletable,
    GlobalDeletable
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
     * [protected description]
     * @var Collection|LengthAwarePaginator
     */
    protected $posts;

    /**
     * Undocumented function
     *
     * @param Post $post
     * @param Carbon $carbon
     */
    public function __construct(Post $post, Carbon $carbon)
    {
        $this->carbon = $carbon;

        $this->post = $post;
    }

    /**
     * Store a newly created Post in storage.
     *
     * @param  array $attributes [description]
     * @return Model              [description]
     */
    public function create(array $attributes) : Model
    {
        $this->post->fill($attributes);
        $this->post->content = $this->post->content_html;

        if ($this->post->status !== Post::INACTIVE) {
            $this->post->published_at =
                $attributes['date_published_at'].$attributes['time_published_at'];
        }

        $this->post->user()->associate(auth()->user());
        $this->post->save();

        $this->post->tag($attributes['tags'] ?? []);

        $this->post->categories()->attach($attributes['categories']);

        return $this->post;
    }

    /**
     * Update Status attribute the specified Post in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes) : bool
    {
        $this->post->status = $attributes['status'];

        if ($this->post->published_at === null) {
            $this->post->published_at = $this->carbon->now();
        }

        return $this->post->save();
    }

    /**
     * Full-Update the specified Post in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateFull(array $attributes) : bool
    {
        $this->post->fill($attributes);
        $this->post->content = $this->post->content_html;

        if ($this->post->status !== Post::INACTIVE) {
            $this->post->published_at =
                $attributes['date_published_at'].$attributes['time_published_at'];
        }

        $this->post->retag($attributes['tags'] ?? []);

        $this->post->categories()->sync($attributes['categories']);

        return $this->post->save();
    }

    /**
     * Mini-Update the specified Post in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        $this->post->title = $attributes['title'];
        $this->post->content_html = $attributes['content_html'];
        $this->post->content = $this->post->content_html;

        return $this->post->save();
    }

    /**
     * Remove the specified Post from storage.
     *
     * @return bool [description]
     */
    public function delete() : bool
    {
        $this->post->categories()->detach();

        $this->post->comments()->delete();

        $this->post->detag();

        return $this->post->delete();
    }

    /**
     * Remove the collection of Posts from storage.
     *
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids) : int
    {
        $this->post->categories()->newPivotStatement()
            ->whereIn('model_id', $ids)
            ->where('model_type', 'N1ebieski\ICore\Models\Post')->delete();

        $this->post->tags()->newPivotStatement()
            ->whereIn('model_id', $ids)
            ->where('model_type', 'N1ebieski\ICore\Models\Post')->delete();

        $this->post->comments()->make()->whereIn('model_id', $ids)
            ->where('model_type', 'N1ebieski\ICore\Models\Post')->delete();

        return $this->post->whereIn('id', $ids)->delete();
    }
}
