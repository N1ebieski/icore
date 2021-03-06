<?php
namespace N1ebieski\ICore\Models\Category;

use Franzose\ClosureTable\Models\ClosureTable;

class CategoryClosure extends ClosureTable implements CategoryClosureInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories_closure';
}
