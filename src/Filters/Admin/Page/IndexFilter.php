<?php

namespace N1ebieski\ICore\Filters\Admin\Page;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Models\Page\Page;

class IndexFilter extends Filter
{
    protected $filters = ['search', 'status', 'orderby', 'parent', 'paginate'];

    public function setParent(Page $page)
    {
        $this->parameters['parent'] = $page;

        return $this;
    }

    public function findParent(int $id = null) : Page
    {
        return Page::find($id, ['id', 'title']);
    }
}
