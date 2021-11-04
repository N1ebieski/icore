<?php

namespace N1ebieski\ICore\Filters\Api\Category;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Filters\Traits\HasExcept;
use N1ebieski\ICore\Filters\Traits\HasParent;
use N1ebieski\ICore\Filters\Traits\HasSearch;
use N1ebieski\ICore\Filters\Traits\HasStatus;
use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;
use N1ebieski\ICore\Filters\Traits\HasCategory;
use N1ebieski\ICore\Filters\Traits\HasPaginate;

class IndexFilter extends Filter
{
    use HasExcept;
    use HasSearch;
    use HasStatus;
    use HasParent;
    use HasCategory;
    use HasOrderBy;
    use HasPaginate;

    /**
     * Undocumented function
     *
     * @param Category $category
     * @return void
     */
    public function setParent(Category $category)
    {
        $this->parameters['parent'] = $category;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $id
     * @return Category
     */
    public function findParent(int $id = null): Category
    {
        return $this->findCategory($id);
    }
}
