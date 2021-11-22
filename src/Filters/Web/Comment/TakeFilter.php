<?php

namespace N1ebieski\ICore\Filters\Web\Comment;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Filters\Traits\HasExcept;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;

class TakeFilter extends Filter
{
    use HasExcept;
    use HasOrderBy;
}
