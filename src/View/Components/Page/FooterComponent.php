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

namespace N1ebieski\ICore\View\Components\Page;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\View\Factory as ViewFactory;

class FooterComponent extends Component
{
    /**
     *
     * @param Page $page
     * @param Collect $collect
     * @param ViewFactory $view
     * @param null|array $pattern
     * @param int $cols
     * @param null|int $maxDepth
     * @return void
     */
    public function __construct(
        protected Page $page,
        protected Collect $collect,
        protected ViewFactory $view,
        protected ?array $pattern = null,
        protected int $cols = 3,
        protected ?int $maxDepth = null
    ) {
        //
    }

    /**
     *
     * @return View
     */
    public function render(): View
    {
        $pages = $this->page->makeCache()->rememberWithRecursiveChildrensByComponent([
            'pattern' => $this->pattern !== null ?
                $this->collect->make($this->pattern)->collapse()->toArray()
                : null
        ]);

        return $this->view->make('icore::web.components.page.footer.index', [
            'pages' => $pages,
            'cols' => $this->pattern ?: (int)ceil($pages->count() / $this->cols),
            'pattern' => $this->pattern,
            'maxDepth' => $this->maxDepth
        ]);
    }
}
