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

namespace N1ebieski\ICore\View\Components;

use Illuminate\Http\Request;
use Illuminate\View\Component;
use N1ebieski\ICore\Models\Link;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\View\Factory as ViewFactory;

class LinkComponent extends Component
{
    /**
     *
     * @param Link $link
     * @param ViewFactory $view
     * @param Collect $collect
     * @param Request $request
     * @param int $limit
     * @param null|array $cats
     * @return void
     */
    public function __construct(
        protected Link $link,
        protected ViewFactory $view,
        protected Collect $collect,
        protected Request $request,
        protected int $limit = 5,
        protected ?array $cats = null
    ) {
        //
    }

    /**
     *
     * @return View
     */
    public function render(): View
    {
        return $this->view->make('icore::web.components.link', [
            'links' => $this->link->makeCache()->rememberLinksByComponent([
                'home' => $this->request->is('/'),
                'cats' => $this->collect->make($this->cats)->flatten()->toArray(),
                'limit' => $this->limit
            ])
        ]);
    }
}
