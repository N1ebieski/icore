<?php

namespace N1ebieski\ICore\Filters\Admin\Category;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Models\Category\Category;

class IndexFilter extends Filter
{
    protected $filters = ['search', 'status', 'parent', 'orderby', 'paginate'];

    public function setParent(Category $category)
    {
        $this->parameters['parent'] = $category;

        return $this;
    }

    public function findParent(int $id = null) : Category
    {
        return parent::findCategory($id);
    }
}
