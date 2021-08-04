<?php

namespace N1ebieski\ICore\Filters\Admin\Page;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Models\Page\Page;
use N1ebieski\ICore\Filters\Traits\HasExcept;
use N1ebieski\ICore\Filters\Traits\HasParent;
use N1ebieski\ICore\Filters\Traits\HasSearch;
use N1ebieski\ICore\Filters\Traits\HasStatus;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;
use N1ebieski\ICore\Filters\Traits\HasPaginate;

class IndexFilter extends Filter
{
    use HasExcept, HasSearch, HasStatus, HasOrderBy, HasParent, HasPaginate;

    /**
     * [setParent description]
     * @param Page $page [description]
     */
    public function setParent(Page $page)
    {
        $this->parameters['parent'] = $page;

        return $this;
    }

    /**
     * [findParent description]
     * @param  int|null $id [description]
     * @return Page       [description]
     */
    public function findParent(int $id = null) : Page
    {
        return Page::find($id, ['id', 'title']);
    }
}
