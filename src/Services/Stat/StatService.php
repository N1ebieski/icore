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

namespace N1ebieski\ICore\Services\Stat;

use N1ebieski\ICore\Models\Stat\Stat;
use Illuminate\Database\DatabaseManager as DB;

class StatService
{
    /**
     * Undocumented function
     *
     * @param Stat $stat
     * @param DB $db
     */
    public function __construct(
        protected Stat $stat,
        protected DB $db
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function increment(): bool
    {
        return $this->db->transaction(function () {
            // @phpstan-ignore-next-line
            $this->stat->morph
                ->stats()
                ->syncWithoutDetaching([
                    $this->stat->id => ['value' => $this->db->raw("`value` + 1")]
                ]);

            return true;
        });
    }
}
