<?php

namespace N1ebieski\ICore\Filters\Admin\User;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Filters\Traits\HasPaginate;
use N1ebieski\ICore\Filters\Traits\HasSearch;
use N1ebieski\ICore\Filters\Traits\HasStatus;
use N1ebieski\ICore\Filters\Traits\HasRole;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;

/**
 * [IndexFilter description]
 */
class IndexFilter extends Filter
{
    use HasSearch, HasStatus, HasRole, HasOrderBy, HasPaginate;
}
