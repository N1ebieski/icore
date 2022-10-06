<?php

namespace N1ebieski\ICore\Models\Category;

use Franzose\ClosureTable\Models\ClosureTable;

/**
 * N1ebieski\ICore\Models\Category\CategoryClosure
 *
 * @property int $closure_id
 * @property int $ancestor
 * @property int $descendant
 * @property int $depth
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryClosure newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryClosure newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryClosure query()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryClosure whereAncestor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryClosure whereClosureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryClosure whereDepth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryClosure whereDescendant($value)
 * @mixin \Eloquent
 */
class CategoryClosure extends ClosureTable
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories_closure';
}
