<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

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
     *
     * @param Request $request
     * @param Collect $collect
     * @param Page $page
     * @return void
     */
    public function __construct(
        Request $request,
        Collect $collect,
        protected Page $page
    ) {
        $this->page = $page;

        parent::__construct($request, $collect);
    }

    /**
     *
     * @param Page $page
     * @return IndexFilter
     */
    public function setParent(Page $page): self
    {
        $this->parameters['parent'] = $page;

        return $this;
    }

    /**
     *
     * @param int $id
     * @return null|Page
     */
    public function findParent(int $id): ?Page
    {
        return $this->page->withAncestorsExceptSelf()
            ->where($this->page->getKeyName(), $id)
            ->first();
    }
}
