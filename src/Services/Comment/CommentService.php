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

namespace N1ebieski\ICore\Services\Comment;

use Throwable;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\ValueObjects\Comment\Status;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CommentService
{
    /**
     * Undocumented function
     *
     * @param Comment $comment
     * @param DB $db
     */
    public function __construct(
        protected Comment $comment,
        protected DB $db
    ) {
        //
    }

    /**
     * Pobiera kolekcję komentarzy pierwszego poziomu wraz z pierwszymi childrenami
     *
     * @param array  $filter Tablica zawierająca parametry filtrowania wyników
     * @return LengthAwarePaginator
     */
    public function getRootsByFilter(array $filter): LengthAwarePaginator
    {
        return $this->paginateChildrens(
            // @phpstan-ignore-next-line
            $this->comment->morph->makeRepo()->paginateCommentsByFilter($filter)
        );
    }

    /**
     * Pobiera kolekcję komentarzy-childrenów wraz z ich pierwszymi childrenami
     *
     * @param array  $filter Tablica zawierająca parametry filtrowania wyników
     * @return LengthAwarePaginator
     */
    public function paginateChildrensByFilter(array $filter): LengthAwarePaginator
    {
        return $this->paginateChildrens(
            $this->comment->makeRepo()
                ->paginateChildrensByFilter($filter)
        );
    }

    /**
     *
     * @param array $attributes
     * @return Comment
     * @throws Throwable
     */
    public function create(array $attributes): Comment
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->comment->content_html = $attributes['content'];
            $this->comment->content = $this->comment->content_html;

            $this->comment->user()->associate($attributes['user']);
            $this->comment->morph()->associate($attributes['morph']);

            $this->comment->parent_id = $attributes['parent_id'] ?? null;

            $this->comment->save();

            return $this->comment;
        });
    }

    /**
     *
     * @param array $attributes
     * @return Comment
     * @throws Throwable
     */
    public function update(array $attributes): Comment
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->comment->content_html = $attributes['content'];
            $this->comment->content = $this->comment->content_html;

            $this->comment->save();

            return $this->comment;
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
            $update = $this->comment->update(['status' => $status]);

            if ($update === true) {
                // Deactivates parent comment, deactivates all descendants
                if ($status == Status::INACTIVE) {
                    $this->comment->descendants()->update(['status' => $status]);
                }

                // Activating child comment, activates all ancestors
                if ($status == Status::ACTIVE) {
                    $this->comment->ancestors()->update(['status' => $status]);
                }
            }

            return $update;
        });
    }

    /**
     * Usuwa Komentarz wraz ze zmianą pozycji pozostałych
     *
     * @return bool
     */
    public function delete(): bool
    {
        return $this->db->transaction(function () {
            return $this->comment->delete();
        });
    }

    /**
     * [deleteGlobal description]
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids): int
    {
        return $this->db->transaction(function () use ($ids) {
            $deleted = 0;

            foreach ($ids as $id) {
                /** @var Comment|null */
                $c = $this->comment->find($id);

                if (!is_null($c)) {
                    $c->makeService()->delete();

                    $deleted += 1;
                }
            }

            return $deleted;
        });
    }

    /**
     * Undocumented function
     *
     * @param LengthAwarePaginator $collection
     * @return LengthAwarePaginator
     */
    protected function paginateChildrens(LengthAwarePaginator $collection): LengthAwarePaginator
    {
        $collection->map(function ($item) {
            $item->setRelation(
                'childrens',
                $item->childrens->paginate(5, null, null, 'page_childrens')
            );

            $item->childrens->map(function ($item) {
                $item->setRelation(
                    'childrens',
                    $item->childrens->paginate(5, null, null, 'page_childrens')
                );
            });

            return $item;
        });

        return $collection;
    }
}
