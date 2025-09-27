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

namespace N1ebieski\ICore\View\ViewModels\Web\Page;

use Spatie\ViewModels\ViewModel;
use N1ebieski\ICore\Models\Page\Page;
use N1ebieski\ICore\Filters\Web\Page\ShowFilter;
use N1ebieski\ICore\Models\Comment\Page\Comment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ShowViewModel extends ViewModel
{
    public function __construct(
        protected readonly Page $page,
        protected readonly Comment $comment,
        protected readonly ShowFilter $filter
    ) {
    }

    public function page(): Page
    {
        return $this->page->makeCache()->rememberLoadSiblingsAndRecursiveChildrens();
    }

    public function comments(): ?LengthAwarePaginator
    {
        return $this->page->comment->isActive() ?
            $this->comment->setRelations(['morph' => $this->page])
                ->makeCache()
                ->rememberRootsByFilter($this->filter->all())
            : null;
    }

    public function filter(): array
    {
        return $this->filter->all();
    }

    public function getFirstChildrenWithContent(Page $page): ?Page
    {
        if (!empty($page->content)) {
            return $page;
        }

        if ($page->relationLoaded('childrensRecursiveWithAllRels')) {
            foreach ($page->childrensRecursiveWithAllRels as $child) {
                return $this->getFirstChildrenWithContent($child);
            }
        }

        return null;
    }
}
