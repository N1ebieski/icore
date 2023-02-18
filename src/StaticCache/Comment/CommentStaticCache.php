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

namespace N1ebieski\ICore\StaticCache\Comment;

use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\StaticCache\StaticCache;

class CommentStaticCache extends StaticCache
{
    /**
     *
     * @param Comment $comment
     * @return void
     */
    public function __construct(
        protected Comment $comment
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function rememberCountByModelTypeAndStatusAndLang(): Collection
    {
        $key = "countByModelTypeAndStatusAndLang";

        return $this->getByKey($key) ?: $this->putResultsByKey(
            $key,
            $this->comment->makeRepo()->countByModelTypeAndStatusAndLang()
        );
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function rememberCountReportedByModelTypeAndLang(): Collection
    {
        $key = "countReportedByModelTypeAndLang";

        return $this->getByKey($key) ?: $this->putResultsByKey(
            $key,
            $this->comment->makeRepo()->countReportedByModelTypeAndLang()
        );
    }
}
