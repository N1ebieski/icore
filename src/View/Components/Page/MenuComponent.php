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
use Illuminate\Contracts\View\Factory as ViewFactory;

class MenuComponent extends Component
{
    /**
     *
     * @param Page $page
     * @param ViewFactory $view
     * @param int $limit
     * @param null|array $pattern
     * @return void
     */
    public function __construct(
        protected Page $page,
        protected ViewFactory $view,
        protected int $limit = 5,
        protected ?array $pattern = null
    ) {
        //
    }

    /**
     *
     * @return View
     */
    public function render(): View
    {
        return $this->view->make('icore::web.components.page.menu', [
            'pages' => $this->page->makeCache()->rememberWithChildrensByComponent([
                'limit' => $this->limit,
                'pattern' => $this->pattern
            ])
        ]);
    }
}
