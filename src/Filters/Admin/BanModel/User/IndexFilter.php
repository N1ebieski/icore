<?php

namespace N1ebieski\ICore\Filters\Admin\BanModel\User;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Filters\Traits\HasSearch;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;
use N1ebieski\ICore\Filters\Traits\HasPaginate;

/**
 * [IndexFilter description]
 */
class IndexFilter extends Filter
{
    use HasSearch, HasOrderBy, HasPaginate;
}
