<?php

namespace N1ebieski\ICore\Filters\Admin\Comment;

use N1ebieski\ICore\Filters\Filter;

class IndexFilter extends Filter
{
    protected $filters = ['author', 'status', 'censored', 'orderby', 'paginate', 'report', 'search'];
}
