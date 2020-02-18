<?php

namespace N1ebieski\ICore\Repositories;

use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * [TagRepo description]
 */
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
    public function firstBySlug(string $slug) : ?Tag
    {
        return $this->tag->whereNormalized($slug)->first();
    }

    /**
     * [getBySearch description]
     * @param  string $name [description]
     * @return Collection        [description]
     */
    public function getBySearch(string $name) : Collection
    {
        return $this->tag->search($name)
            ->orderByRaw('LENGTH(name) ASC')
            ->limit(5)
            ->get(['name']);
    }

    /**
     * Undocumented function
     *
     * @param array $component
     * @return Collection
     */
    public function getPopularByComponent(array $component) : Collection
    {
        return $this->tag->selectRaw('`tags`.*, COUNT(`tags`.`tag_id`) AS taggable_count')
            ->leftJoin('tags_models', 'tags.tag_id', '=', 'tags_models.tag_id')
            ->poliType()
            ->when($component['cats'] !== null, function ($query) use ($component) {
                $query->whereExists(function ($query) use ($component) {
                    $query->select(DB::raw(1))
                        ->from('categories_models')
                        ->where(function ($query) use ($component) {
                            $query->whereColumn('categories_models.model_id', 'tags_models.model_id')
                                ->where('categories_models.model_type', $this->tag->model_type)
                                ->whereIn('categories_models.category_id', $component['cats']);
                        });
                });
            })
            ->groupBy('tags.tag_id')
            ->having('taggable_count', '>=', 1)
            ->orderBy('taggable_count', 'desc')
            ->limit($component['limit'])
            ->get()
            ->shuffle();
    }
}
