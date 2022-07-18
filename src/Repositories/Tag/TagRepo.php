<?php

namespace N1ebieski\ICore\Repositories\Tag;

use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TagRepo
{
    /**
     * [private description]
     * @var Tag
     */
    protected $tag;

    /**
     * [__construct description]
     * @param Tag $tag [description]
     */
    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    /**
     * [firstBySlug description]
     * @param  string $slug [description]
     * @return Tag|null       [description]
     */
    public function firstBySlug(string $slug): ?Tag
    {
        return $this->tag->whereNormalized($slug)->first();
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
    {
        return $this->tag->selectRaw("`{$this->tag->getTable()}`.*")
            ->filterExcept($filter['except'])
            ->filterSearch($filter['search'])
            ->when(strpos($filter['orderby'], 'sum') !== false, function ($query) {
                $query->withSum();
            })
            ->when($filter['orderby'] === null, function ($query) use ($filter) {
                $query->filterOrderBySearch($filter['search']);
            })
            ->filterOrderBy($filter['orderby'])
            ->filterPaginate($filter['paginate']);
    }

    /**
     * Undocumented function
     *
     * @param array $component
     * @return Collection
     */
    public function getPopularByComponent(array $component): Collection
    {
        $morph = $this->tag->morphs()->make();

        return $this->tag->selectRaw('`tags`.*, COUNT(`tags`.`tag_id`) AS taggable_count')
            ->join('tags_models', 'tags.tag_id', '=', 'tags_models.tag_id')
            ->join("{$morph->getTable()}", function ($query) use ($morph) {
                $query->on('tags_models.model_id', '=', "{$morph->getTable()}.id")
                    ->where("{$morph->getTable()}.status", $morph->status::ACTIVE);
            })
            ->where('tags_models.model_type', $this->tag->model_type)
            ->when($component['cats'] !== null, function ($query) use ($component) {
                $query->join('categories_models', function ($query) use ($component) {
                    $query->on('tags_models.model_id', '=', 'categories_models.model_id')
                        ->where('categories_models.model_type', $this->tag->model_type)
                        ->whereIn('categories_models.category_id', $component['cats']);
                });
            })
            ->groupBy('tags.tag_id')
            ->havingRaw('COUNT(`tags`.`tag_id`) >= 1')
            ->orderBy('taggable_count', 'desc')
            ->limit($component['limit'])
            ->get()
            ->shuffle();
    }
}
