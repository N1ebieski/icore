<?php

namespace N1ebieski\ICore\Filters\Admin\Category;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Filters\Traits\HasSearch;
use N1ebieski\ICore\Filters\Traits\HasStatus;
use N1ebieski\ICore\Filters\Traits\HasParent;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;
use N1ebieski\ICore\Filters\Traits\HasPaginate;

/**
 * [IndexFilter description]
 */
class IndexFilter extends Filter
{
    use HasSearch, HasStatus, HasParent, HasOrderBy, HasPaginate;

    /**
     * [setParent description]
     * @param Category $category [description]
     */
    public function setParent(Category $category)
    {
        $this->parameters['parent'] = $category;

        return $this;
    }

    /**
     * [findParent description]
     * @param  int|null   $id [description]
     * @return Category     [description]
     */
    public function findParent(int $id = null) : Category
    {
        return parent::findCategory($id);
    }
}
