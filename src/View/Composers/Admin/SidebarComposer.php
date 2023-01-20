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

namespace N1ebieski\ICore\View\Composers\Admin;

use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\View\Composers\Composer;
use N1ebieski\ICore\ValueObjects\Comment\Status;
use Illuminate\Contracts\Config\Repository as Config;

class SidebarComposer extends Composer
{
    /**
     *
     * @param Comment $comment
     * @param Config $config
     * @return void
     */
    public function __construct(
        protected Comment $comment,
        protected Config $config
    ) {
        //
    }

    /**
     *
     * @return Collection
     */
    public function commentsInactiveCount(): Collection
    {
        return $this->comment->makeRepo()->countByModelTypeAndStatusAndLang()
            ->where('lang', $this->config->get('app.locale'))
            ->where('status', Status::inactive());
    }

    /**
     *
     * @return Collection
     */
    public function commentsReportedCount(): Collection
    {
        return $this->comment->makeRepo()->countReportedByModelTypeAndLang()
            ->where('lang', $this->config->get('app.locale'));
    }
}
