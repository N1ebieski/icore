<?php

namespace N1ebieski\ICore\Filters\Admin\BanValue;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Filters\Traits\HasSearch;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;
use N1ebieski\ICore\Filters\Traits\HasPaginate;

class IndexFilter extends Filter
{
    use HasSearch;
    use HasOrderBy;
    use HasPaginate;
}
