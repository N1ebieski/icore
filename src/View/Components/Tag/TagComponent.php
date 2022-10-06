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

namespace N1ebieski\ICore\View\Components\Tag;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Contracts\View\Factory as ViewFactory;

class TagComponent extends Component
{
    /**
     *
     * @param Tag $tag
     * @param ViewFactory $view
     * @param int $limit
     * @param null|array $colors
     * @return void
     */
    public function __construct(
        protected Tag $tag,
        protected ViewFactory $view,
        protected int $limit = 25,
        protected ?array $colors = null
    ) {
        //
    }

    /**
     *
     * @return View
     */
    public function render(): View
    {
        return $this->view->make('icore::web.components.tag.tag', [
            'tags' => $this->tag->makeCache()->rememberPopularByComponent([
                'limit' => $this->limit,
            ]),
            'colors' => $this->colors
        ]);
    }
}
