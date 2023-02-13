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

namespace N1ebieski\ICore\Models\Category\Post;

use Illuminate\Support\Facades\App;
use N1ebieski\ICore\ValueObjects\AutoTranslate;
use Illuminate\Database\Eloquent\Casts\Attribute;
use N1ebieski\ICore\ValueObjects\Category\Status;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\Models\Category\Category as BaseCategory;
use N1ebieski\ICore\Http\Resources\Category\Post\CategoryResource;
use N1ebieski\ICore\Database\Factories\Category\Post\CategoryFactory;

/**
 * N1ebieski\ICore\Models\Category\Post\Category
 *
 * @property int $id
 * @property string $model_type
 * @property string $slug
 * @property string|null $icon
 * @property string $name
 * @property Status $status
 * @property int $parent_id
 * @property int $position
 * @property int $real_depth
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Category[] $ancestors
 * @property-read int|null $ancestors_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Category[] $children
 * @property-read int|null $children_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Category[] $childrens
 * @property-read int|null $childrens_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Category[] $childrensRecursiveWithAllRels
 * @property-read int|null $childrens_recursive_with_all_rels_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Category[] $descendants
 * @property-read int|null $descendants_count
 * @property-read string $created_at_diff
 * @property-read string $poli
 * @property-read int $real_position
 * @property-read string $updated_at_diff
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Post[] $morphs
 * @property-read int|null $morphs_count
 * @property-read Category|null $parent
 * @method static Builder|Category active()
 * @method static \Franzose\ClosureTable\Extensions\Collection|static[] all($columns = ['*'])
 * @method static Builder|Entity ancestors()
 * @method static Builder|Entity ancestorsOf($id)
 * @method static Builder|Entity ancestorsWithSelf()
 * @method static Builder|Entity ancestorsWithSelfOf($id)
 * @method static Builder|Entity childAt($position)
 * @method static Builder|Entity childNode()
 * @method static Builder|Entity childNodeOf($id)
 * @method static Builder|Entity childOf($id, $position)
 * @method static Builder|Entity childrenRange($from, $to = null)
 * @method static Builder|Entity childrenRangeOf($id, $from, $to = null)
 * @method static Builder|Entity descendants()
 * @method static Builder|Entity descendantsOf($id)
 * @method static Builder|Entity descendantsWithSelf()
 * @method static Builder|Entity descendantsWithSelfOf($id)
 * @method static \N1ebieski\ICore\Database\Factories\Category\Post\CategoryFactory factory(...$parameters)
 * @method static Builder|Category filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|Category filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|Category filterExcept(?array $except = null)
 * @method static Builder|Category filterOrderBy(?string $orderby = null)
 * @method static Builder|Category filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|Category filterParent($parent = null)
 * @method static Builder|Category filterReport(?int $report = null)
 * @method static Builder|Category filterSearch(?string $search = null)
 * @method static Builder|Category filterStatus(?int $status = null)
 * @method static Builder|Category findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static Builder|Entity firstChild()
 * @method static Builder|Entity firstChildOf($id)
 * @method static Builder|Entity firstSibling()
 * @method static Builder|Entity firstSiblingOf($id)
 * @method static \Franzose\ClosureTable\Extensions\Collection|static[] get($columns = ['*'])
 * @method static Builder|Entity lastChild()
 * @method static Builder|Entity lastChildOf($id)
 * @method static Builder|Entity lastSibling()
 * @method static Builder|Entity lastSiblingOf($id)
 * @method static Builder|Entity neighbors()
 * @method static Builder|Entity neighborsOf($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static Builder|Entity nextSibling()
 * @method static Builder|Entity nextSiblingOf($id)
 * @method static Builder|Entity nextSiblings()
 * @method static Builder|Entity nextSiblingsOf($id)
 * @method static Builder|Category orderBySearch(string $term)
 * @method static Builder|Category poli()
 * @method static Builder|Category poliType()
 * @method static Builder|Entity prevSibling()
 * @method static Builder|Entity prevSiblingOf($id)
 * @method static Builder|Entity prevSiblings()
 * @method static Builder|Entity prevSiblingsOf($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static Builder|Category root()
 * @method static Builder|Category search(string $term)
 * @method static Builder|Category sibling()
 * @method static Builder|Entity siblingAt($position)
 * @method static Builder|Entity siblingOf($id)
 * @method static Builder|Entity siblingOfAt($id, $position)
 * @method static Builder|Entity siblings()
 * @method static Builder|Entity siblingsOf($id)
 * @method static Builder|Entity siblingsRange($from, $to = null)
 * @method static Builder|Entity siblingsRangeOf($id, $from, $to = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereRealDepth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @method static Builder|Category withAncestorsExceptSelf()
 * @method static Builder|Category withRecursiveAllRels()
 * @method static Builder|Category withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @mixin \Eloquent
 */
class Category extends BaseCategory
{
    // Configuration

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'model_type' => \N1ebieski\ICore\Models\Post::class,
        'status' => Status::ACTIVE,
        'auto_translate' => AutoTranslate::INACTIVE
    ];

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return \N1ebieski\ICore\Models\Category\Category::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \N1ebieski\ICore\Database\Factories\Category\Post\CategoryFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\ICore\Database\Factories\Category\Post\CategoryFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function morphs(): MorphToMany
    {
        return $this->morphedByMany(\N1ebieski\ICore\Models\Post::class, 'model', 'categories_models', 'category_id');
    }

    // Attributes

    /**
     *
     * @return Attribute
     */
    public function poli(): Attribute
    {
        return new Attribute(fn (): string => 'post');
    }

    // Factories

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return CategoryFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }

    /**
     * [makeRepo description]
     * @return CategoryResource [description]
     */
    public function makeResource()
    {
        return App::make(CategoryResource::class, ['category' => $this]);
    }
}
