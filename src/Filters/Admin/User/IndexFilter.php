<?php

namespace N1ebieski\ICore\Filters\Admin\User;

use N1ebieski\ICore\Filters\Filter;

class IndexFilter extends Filter
{
    protected $filters = ['search', 'status', 'role', 'orderby', 'paginate'];
}
