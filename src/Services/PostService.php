<?php

namespace N1ebieski\ICore\Services;

use Carbon\Carbon;
use N1ebieski\ICore\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Deletable;
use N1ebieski\ICore\Services\Interfaces\Updatable;
use N1ebieski\ICore\Services\Interfaces\FullUpdatable;
use N1ebieski\ICore\Services\Interfaces\GlobalDeletable;
use N1ebieski\ICore\Services\Interfaces\StatusUpdatable;

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
     * Undocumented variable
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * Undocumented function
     *
     * @param Post $post
     * @param Carbon $carbon
     * @param Auth $auth
     * @param DB $db
     */
    public function __construct(
        Post $post,
        Carbon $carbon,
        Auth $auth,
        DB $db
    ) {
        $this->setPost($post);

        $this->carbon = $carbon;
        $this->auth = $auth;
        $this->db = $db;
    }

    /**
     * Undocumented function
     *
     * @param Post $post
     * @return static
     */
    public function setPost(Post $post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Store a newly created Post in storage.
     *
     * @param  array $attributes [description]
     * @return Model              [description]
     */
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->post->fill($attributes);
            $this->post->content = $this->post->content_html;

            if ($this->post->status !== Post::INACTIVE) {
                $this->post->published_at =
                    $attributes['date_published_at'] . $attributes['time_published_at'];
            }

            $this->post->user()->associate($this->auth->user());

            $this->post->save();

            if (array_key_exists('tags', $attributes)) {
                $this->post->tag($attributes['tags'] ?? []);
            }

            if (array_key_exists('categories', $attributes)) {
                $this->post->categories()->attach($attributes['categories'] ?? []);
            }

            return $this->post;
        });
    }

    /**
     * Update Status attribute the specified Post in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->post->status = $attributes['status'];

            if ($this->post->published_at === null) {
                $this->post->published_at = $this->carbon->now();
            }

            return $this->post->save();
        });
    }

    /**
     * Full-Update the specified Post in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateFull(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->post->fill($attributes);
            $this->post->content = $this->post->content_html;

            if ($this->post->status !== Post::INACTIVE) {
                $this->post->published_at =
                    $attributes['date_published_at'] . $attributes['time_published_at'];
            }

            if (array_key_exists('tags', $attributes)) {
                $this->post->retag($attributes['tags'] ?? []);
            }

            if (array_key_exists('user', $attributes)) {
                $this->post->user()->associate($attributes['user']);
            }

            if (array_key_exists('categories', $attributes)) {
                $this->post->categories()->sync($attributes['categories'] ?? []);
            }

            return $this->post->save();
        });
    }

    /**
     * Mini-Update the specified Post in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->post->title = $attributes['title'];
            $this->post->content_html = $attributes['content_html'];
            $this->post->content = $this->post->content_html;

            return $this->post->save();
        });
    }

    /**
     * Remove the specified Post from storage.
     *
     * @return bool [description]
     */
    public function delete(): bool
    {
        return $this->db->transaction(function () {
            $this->post->categories()->detach();

            $this->post->comments()->delete();

            $this->post->stats()->detach();

            $this->post->detag();

            return $this->post->delete();
        });
    }

    /**
     * Remove the collection of Posts from storage.
     *
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids): int
    {
        return $this->db->transaction(function () use ($ids) {
            $this->post->categories()->newPivotStatement()
                ->whereIn('model_id', $ids)
                ->where('model_type', 'N1ebieski\ICore\Models\Post')->delete();

            $this->post->tags()->newPivotStatement()
                ->whereIn('model_id', $ids)
                ->where('model_type', 'N1ebieski\ICore\Models\Post')->delete();

            $this->post->stats()->newPivotStatement()
                ->whereIn('model_id', $ids)
                ->where('model_type', $this->post->getMorphClass())->delete();

            $this->post->comments()->make()->whereIn('model_id', $ids)
                ->where('model_type', 'N1ebieski\ICore\Models\Post')->delete();

            return $this->post->whereIn('id', $ids)->delete();
        });
    }
}
