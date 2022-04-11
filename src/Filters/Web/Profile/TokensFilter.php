<?php

namespace N1ebieski\ICore\Filters\Web\Profile;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Filters\Traits\HasExcept;
use N1ebieski\ICore\Filters\Traits\HasSearch;
use N1ebieski\ICore\Filters\Traits\HasStatus;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;
use N1ebieski\ICore\Filters\Traits\HasPaginate;

class TokensFilter extends Filter
{
    use HasExcept;
    use HasSearch;
    use HasStatus;
    use HasOrderBy;
    use HasPaginate;
}