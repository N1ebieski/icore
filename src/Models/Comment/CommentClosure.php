<?php
namespace N1ebieski\ICore\Models\Comment;

use Franzose\ClosureTable\Models\ClosureTable;

class CommentClosure extends ClosureTable implements CommentClosureInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'comments_closure';
}
