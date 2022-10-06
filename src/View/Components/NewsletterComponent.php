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

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use N1ebieski\ICore\Models\Newsletter;
use Illuminate\Contracts\View\Factory as ViewFactory;

class NewsletterComponent extends Component
{
    /**
     * Undocumented function
     *
     * @param Newsletter $newsletter
     * @param ViewFactory $view
     */
    public function __construct(
        protected Newsletter $newsletter,
        protected ViewFactory $view
    ) {
        //
    }

    /**
     *
     * @return View
     */
    public function render(): View
    {
        return $this->view->make('icore::web.components.newsletter');
    }
}
