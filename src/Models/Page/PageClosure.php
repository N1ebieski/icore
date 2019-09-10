<?php
namespace N1ebieski\ICore\Models\Page;

use Franzose\ClosureTable\Models\ClosureTable;

class PageClosure extends ClosureTable implements PageClosureInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pages_closure';
}
