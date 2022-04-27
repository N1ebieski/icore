<?php

namespace N1ebieski\ICore\Services;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\ValueObjects\Comment\Status;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Deletable;
use N1ebieski\ICore\Services\Interfaces\Updatable;
use N1ebieski\ICore\Services\Interfaces\StatusUpdatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CommentService implements Creatable, Updatable, StatusUpdatable, Deletable
{
    /**
     * Comment model
     * @var Comment
     */
    protected $comment;

    /**
     * [private description]
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
     * @param Comment $comment
     * @param Auth $auth
     * @param DB $db
     */
    public function __construct(Comment $comment, Auth $auth, DB $db)
    {
        $this->comment = $comment;

        $this->auth = $auth;
        $this->db = $db;
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
            $this->comment->morph->makeRepo()
                ->paginateCommentsByFilter($filter)
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
     * Tworzy nowy komentarz powiązany z modelem
     *
     * @param  array $attributes
     * @return Model
     */
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->comment->content_html = $attributes['content'];
            $this->comment->content = $this->comment->content_html;

            $this->comment->user()->associate($this->auth->user());
            $this->comment->morph()->associate($this->comment->morph);

            $this->comment->parent_id = $attributes['parent_id'] ?? null;

            $this->comment->save();

            return $this->comment;
        });
    }

    /**
     * Edytuje istniejący komentarz
     *
     * @param  array $attributes
     * @return bool
     */
    public function update(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->comment->content_html = $attributes['content'];
            $this->comment->content = $this->comment->content_html;

            return $this->comment->save();
        });
    }

    /**
     * Zmienia status komentarza i jego przodkom/potomkom
     *
     * @param  array $attributes
     * @return bool
     */
    public function updateStatus(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            $update = $this->comment->update(['status' => $attributes['status']]);

            if ($update === true) {
                // Deactivates parent comment, deactivates all descendants
                if ($attributes['status'] == Status::INACTIVE) {
                    $this->comment->descendants()->update(['status' => $attributes['status']]);
                }

                // Activating child comment, activates all ancestors
                if ($attributes['status'] == Status::ACTIVE) {
                    $this->comment->ancestors()->update(['status' => $attributes['status']]);
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
                if ($c = $this->comment->find($id)) {
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
    protected static function paginateChildrens(LengthAwarePaginator $collection): LengthAwarePaginator
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
