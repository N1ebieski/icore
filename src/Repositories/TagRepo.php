<?php

namespace N1ebieski\ICore\Repositories;

use N1ebieski\ICore\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

/**
 * [TagRepo description]
 */
class TagRepo
{
    /**
     * [private description]
     * @var Tag
     */
    private $tag;

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
}
