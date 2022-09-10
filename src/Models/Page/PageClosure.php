<?php

namespace N1ebieski\ICore\Models\Page;

use Franzose\ClosureTable\Models\ClosureTable;

/**
 * N1ebieski\ICore\Models\Page\PageClosure
 *
 * @property int $closure_id
 * @property int $ancestor
 * @property int $descendant
 * @property int $depth
 * @method static \Illuminate\Database\Eloquent\Builder|PageClosure newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PageClosure newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PageClosure query()
 * @method static \Illuminate\Database\Eloquent\Builder|PageClosure whereAncestor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageClosure whereClosureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageClosure whereDepth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageClosure whereDescendant($value)
 * @mixin \Eloquent
 */
class PageClosure extends ClosureTable
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pages_closure';
}
