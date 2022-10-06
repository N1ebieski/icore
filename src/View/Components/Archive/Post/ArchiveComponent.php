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

namespace N1ebieski\ICore\View\Components\Archive\Post;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use N1ebieski\ICore\Models\Post;
use Illuminate\Contracts\View\Factory as ViewFactory;

class ArchiveComponent extends Component
{
    /**
     * Undocumented function
     *
     * @param Post $post
     * @param ViewFactory $view
     */
    public function __construct(protected Post $post, protected ViewFactory $view)
    {
        //
    }

    /**
     *
     * @return View
     */
    public function render(): View
    {
        return $this->view->make('icore::web.components.archive.post.archive', [
            'archives' => $this->post->makeCache()->rememberArchives()
        ]);
    }
}
