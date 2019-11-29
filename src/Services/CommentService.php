<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use N1ebieski\ICore\Services\Serviceable;

/**
 * [CommentService description]
 */
class CommentService implements Serviceable
{
    /**
     * Comment model
     * @var Comment
     */
    protected $comment;

    /**
     * Kolekcja zawierająca komentarze przeznaczone do wyświelenia na froncie
     * @var Collection
     */
    protected $comments;

    /**
     * [__construct description]
     * @param Comment      $comment      [description]
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Pobiera kolekcję komentarzy pierwszego poziomu wraz z pierwszymi childrenami
     *
     * @param array  $filter Tablica zawierająca parametry filtrowania wyników
     * @return LengthAwarePaginator
     */
    public function getRootsByFilter(array $filter) : LengthAwarePaginator
    {
        $this->comments = $this->comment->getMorph()->makeRepo()->paginateCommentsByFilter($filter);

        $this->comments = $this->paginateChildrens();

        return $this->comments;
    }

    /**
     * Pobiera kolekcję komentarzy-childrenów wraz z ich pierwszymi childrenami
     *
     * @param array  $filter Tablica zawierająca parametry filtrowania wyników
     * @return LengthAwarePaginator
     */
    public function paginateChildrensByFilter(array $filter) : LengthAwarePaginator
    {
        $this->comments = $this->comment->makeRepo()->paginateChildrensByFilter($filter);

        $this->comments = $this->paginateChildrens();

        return $this->comments;
    }

    /**
     * Metoda pomocnicza do tworzenia paginacji relacji childrens
     *
     * @return LengthAwarePaginator
     */
    protected function paginateChildrens() : LengthAwarePaginator
    {
        $this->comments->map(function($item) {
            $item->setRelation('childrens', $item->childrens
            ->paginate(5, null, null, 'page_childrens'));
            $item->childrens->map(function($i) {
                $i->setRelation('childrens', $i->childrens
                ->paginate(5, null, null, 'page_childrens'));
            });
        });

        return $this->comments;
    }

    /**
     * Tworzy nowy komentarz powiązany z modelem
     *
     * @param  array $attributes
     * @return Model
     */
    public function create(array $attributes) : Model
    {
        $this->comment->content_html = $attributes['content'];
        $this->comment->content = $this->comment->content_html;

        $this->comment->user()->associate(auth()->user()->id);
        $this->comment->morph()->associate($this->comment->getMorph());

        $this->comment->parent_id = $attributes['parent_id'] ?? null;

        $this->comment->save();

        return $this->comment;
    }

    /**
     * Edytuje istniejący komentarz
     *
     * @param  array $attributes
     * @return bool
     */
    public function update(array $attributes) : bool
    {
        $this->comment->content_html = $attributes['content'];
        $this->comment->content = $this->comment->content_html;

        return $this->comment->save();
    }

    /**
     * Zmienia status komentarza i jego przodkom/potomkom
     *
     * @param  array $attributes
     * @return bool
     */
    public function updateStatus(array $attributes) : bool
    {
        $updateStatus = $this->comment->update(['status' => $attributes['status']]);

        if ($updateStatus === true) {
            // Deaktywacja komentarza nadrzędnego, deaktywuje wszystkich potomków
            if ($attributes['status'] == 0) {
                $this->comment->descendants()->update(['status' => $attributes['status']]);
            }

            // Aktywacja komentarza podrzędnego, aktywuje wszystkich przodków
            if ($attributes['status'] == 1) {
                $this->comment->ancestors()->update(['status' => $attributes['status']]);
            }
        }

        return $updateStatus;
    }

    /**
     * Usuwa Komentarz wraz ze zmianą pozycji pozostałych
     *
     * @return bool
     */
    public function delete() : bool
    {
        $delete = $this->comment->delete();
        // $this->category->deleteSubtree(true, true);

        if ($delete === true) {
            // Zmniejszamy pozycje rodzeństwa o jeden bo ClosureTable nie robi tego
            // z automatu podczas usuwania (nie wiem czemu?)
            $this->comment->where([
                ['parent_id', $this->comment->parent_id],
                ['position', '>', $this->comment->position]
            ])->decrement('position');
        }

        return $delete;
    }

    /**
     * [deleteGlobal description]
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids) : int {}
}
