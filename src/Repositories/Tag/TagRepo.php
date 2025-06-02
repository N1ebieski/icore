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

namespace N1ebieski\ICore\Repositories\Tag;

use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TagRepo
{
    /**
     * [__construct description]
     * @param Tag $tag [description]
     */
    public function __construct(
        protected Tag $tag,
        protected Auth $auth
    ) {
        //
    }

    /**
     * [firstBySlug description]
     * @param  string $slug [description]
     * @return Tag|null       [description]
     */
    public function firstBySlug(string $slug): ?Tag
    {
        return $this->tag->newQuery()->whereNormalized($slug)->lang()->first();
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
    {
        return $this->tag->newQuery()
            ->selectRaw("`{$this->tag->getTable()}`.*")
            ->lang()
            ->filterExcept($filter['except'])
            ->when(!is_null($filter['search']), function (Builder|Tag $query) use ($filter) {
                return $query->filterSearch($filter['search'])
                    ->when($this->auth->user()?->can('admin.tags.view'), function (Builder $query) {
                        return $query->where(function (Builder $query) {
                            foreach ([$this->tag->getKeyName()] as $attr) {
                                $query = $query->when(array_key_exists($attr, $this->tag->search), function (Builder $query) use ($attr) {
                                    return $query->where("{$this->tag->getTable()}.{$attr}", $this->tag->search[$attr]);
                                });
                            }

                            return $query;
                        });
                    });
            })
            ->when(strpos($filter['orderby'], 'sum') !== false, function (Builder|Tag $query) {
                return $query->withCountSum();
            })
            ->when(is_null($filter['orderby']), function (Builder|Tag $query) use ($filter) {
                return $query->filterOrderBySearch($filter['search']);
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
        /** @var mixed */
        $morph = $this->tag->morphs()->make();

        return $this->tag->newQuery()
            ->selectRaw('`tags`.*, COUNT(`tags`.`tag_id`) AS taggable_count')
            ->join('tags_models', 'tags.tag_id', '=', 'tags_models.tag_id')
            ->join("{$morph->getTable()}", function (JoinClause $query) use ($morph) {
                return $query->on('tags_models.model_id', '=', "{$morph->getTable()}.id")
                    ->where("{$morph->getTable()}.status", $morph->status::ACTIVE);
            })
            ->where('tags_models.model_type', $morph->getMorphClass())
            ->when($component['cats'] !== null, function (Builder $query) use ($morph, $component) {
                return $query->join('categories_models', function (JoinClause $query) use ($morph, $component) {
                    return $query->on('tags_models.model_id', '=', 'categories_models.model_id')
                        ->where('categories_models.model_type', $morph->getMorphClass())
                        ->whereIn('categories_models.category_id', $component['cats']);
                });
            })
            ->lang()
            ->groupBy('tags.tag_id')
            ->havingRaw('COUNT(`tags`.`tag_id`) >= 1')
            ->orderBy('taggable_count', 'desc')
            ->limit($component['limit'])
            ->get()
            ->shuffle();
    }
}
