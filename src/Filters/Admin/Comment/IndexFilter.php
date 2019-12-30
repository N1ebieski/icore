<?php

namespace N1ebieski\ICore\Filters\Admin\Comment;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Filters\Traits\HasAuthor;
use N1ebieski\ICore\Filters\Traits\HasStatus;
use N1ebieski\ICore\Filters\Traits\HasCensored;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;
use N1ebieski\ICore\Filters\Traits\HasPaginate;
use N1ebieski\ICore\Filters\Traits\HasReport;
use N1ebieski\ICore\Filters\Traits\HasSearch;

/**
 * [IndexFilter description]
 */
class IndexFilter extends Filter
{
    use HasAuthor, HasStatus, HasCensored, HasOrderBy, HasPaginate, HasReport, HasSearch;
}
