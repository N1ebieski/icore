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

namespace N1ebieski\ICore\Repositories\Stat;

use N1ebieski\ICore\Models\Stat\Stat;

class StatRepo
{
    /**
     * [__construct description]
     * @param Stat $stat [description]
     */
    public function __construct(protected Stat $stat)
    {
        //
    }

    /**
     * [firstBySlug description]
     * @param  string $slug [description]
     * @return Stat|null       [description]
     */
    public function firstBySlug(string $slug): ?Stat
    {
        return $this->stat->newQuery()->where('slug', $slug)->first();
    }
}
