<?php

namespace N1ebieski\ICore\Filters\Admin\Post;

use N1ebieski\ICore\Filters\Filter;

class IndexFilter extends Filter
{
    protected $filters = ['search', 'status', 'orderby', 'category', 'paginate'];
}
