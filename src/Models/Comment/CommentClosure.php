<?php

namespace N1ebieski\ICore\Models\Comment;

use Franzose\ClosureTable\Models\ClosureTable;

/**
 * N1ebieski\ICore\Models\Comment\CommentClosure
 *
 * @property int $closure_id
 * @property int $ancestor
 * @property int $descendant
 * @property int $depth
 * @method static \Illuminate\Database\Eloquent\Builder|CommentClosure newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentClosure newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentClosure query()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentClosure whereAncestor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommentClosure whereClosureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommentClosure whereDepth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommentClosure whereDescendant($value)
 * @mixin \Eloquent
 */
class CommentClosure extends ClosureTable
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'comments_closure';
}
