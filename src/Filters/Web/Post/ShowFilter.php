<?php

namespace N1ebieski\ICore\Filters\Web\Post;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Filters\Traits\HasExcept;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;

class ShowFilter extends Filter
{
    use HasExcept, HasOrderBy;
}
