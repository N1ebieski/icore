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

namespace N1ebieski\ICore\Services\Post;

use Throwable;
use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\ValueObjects\Post\Status;
use Illuminate\Database\DatabaseManager as DB;

class PostService
{
    /**
     * Undocumented function
     *
     * @param Post $post
     * @param Carbon $carbon
     * @param DB $db
     */
    public function __construct(
        protected Post $post,
        protected Carbon $carbon,
        protected DB $db
    ) {
        //
    }

    /**
     *
     * @param array $attributes
     * @return Post
     * @throws Throwable
     */
    public function create(array $attributes): Post
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->post->fill($attributes);
            $this->post->content = $this->post->content_html;

            if (!$this->post->status->isInactive()) {
                // @phpstan-ignore-next-line
                $this->post->published_at =
                    $attributes['date_published_at'] . $attributes['time_published_at'];
            }

            $this->post->user()->associate($attributes['user']);

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
     *
     * @param int $status
     * @return bool
     * @throws Throwable
     */
    public function updateStatus(int $status): bool
    {
        return $this->db->transaction(function () use ($status) {
            // @phpstan-ignore-next-line
            $this->post->status = $status;

            if ($this->post->published_at === null) {
                $this->post->published_at = $this->carbon->now();
            }

            return $this->post->save();
        });
    }

    /**
     *
     * @param array $attributes
     * @return Post
     * @throws Throwable
     */
    public function updateFull(array $attributes): Post
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->post->fill($attributes);
            $this->post->content = $this->post->content_html;

            if (!$this->post->status->isInactive()) {
                // @phpstan-ignore-next-line
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

            $this->post->save();

            return $this->post;
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
     * [updateActivateScheduled description]
     * @return int              [description]
     */
    public function activateScheduled(): int
    {
        return $this->db->transaction(function () {
            return $this->post->newQuery()
                ->whereDate('published_at', '<', $this->carbon->now()->format('Y-m-d'))
                ->orWhere(function (Builder $query) {
                    $query->whereDate('published_at', '=', $this->carbon->now()->format('Y-m-d'))
                        ->whereTime('published_at', '<=', $this->carbon->now()->format('H:i:s'));
                })
                ->scheduled()
                ->update(['status' => Status::ACTIVE]);
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
                ->where('model_type', $this->post->getMorphClass())->delete();

            $this->post->tags()->newPivotStatement()
                ->whereIn('model_id', $ids)
                ->where('model_type', $this->post->getMorphClass())->delete();

            $this->post->stats()->newPivotStatement()
                ->whereIn('model_id', $ids)
                ->where('model_type', $this->post->getMorphClass())->delete();

            $this->post->comments()->make()->whereIn('model_id', $ids)
                ->where('model_type', $this->post->getMorphClass())->delete();

            return $this->post->whereIn('id', $ids)->delete();
        });
    }
}
