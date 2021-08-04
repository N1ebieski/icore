<?php

namespace N1ebieski\ICore\Filters\Api\User;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Filters\Traits\HasRole;
use N1ebieski\ICore\Filters\Traits\HasExcept;
use N1ebieski\ICore\Filters\Traits\HasSearch;
use N1ebieski\ICore\Filters\Traits\HasStatus;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;
use N1ebieski\ICore\Filters\Traits\HasPaginate;

class IndexFilter extends Filter
{
    use HasSearch, HasStatus, HasRole, HasOrderBy, HasPaginate, HasExcept;
}
