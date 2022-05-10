<?php

namespace N1ebieski\ICore\Filters\Admin\Page;

use Illuminate\Http\Request;
use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Filters\Traits\HasExcept;
use N1ebieski\ICore\Filters\Traits\HasParent;
use N1ebieski\ICore\Filters\Traits\HasSearch;
use N1ebieski\ICore\Filters\Traits\HasStatus;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;
use N1ebieski\ICore\Filters\Traits\HasPaginate;

class IndexFilter extends Filter
{
    use HasExcept;
    use HasSearch;
    use HasStatus;
    use HasOrderBy;
    use HasParent;
    use HasPaginate;

    /**
     * @var Page
     */
    protected $page;

    /**
     *
     * @param Request $request
     * @param Collect $collect
     * @param Page $page
     * @return void
     */
    public function __construct(Request $request, Collect $collect, Page $page)
    {
        $this->page = $page;

        parent::__construct($request, $collect);
    }

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
     * @return Page|null       [description]
     */
    public function findParent(int $id = null): ?Page
    {
        return $id !== null ?
            $this->page->withAncestorsExceptSelf()
                ->where($this->page->getKeyName(), $id)
                ->first()
            : null;
    }
}
