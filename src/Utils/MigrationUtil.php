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

namespace N1ebieski\ICore\Utils;

use Illuminate\Support\Str;
use N1ebieski\ICore\Cache\Migration\MigrationCache;

class MigrationUtil
{
    /**
     * Undocumented function
     *
     * @param MigrationCache $migrationCache
     * @param Str $str
     */
    public function __construct(
        protected MigrationCache $migrationCache,
        protected Str $str
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @param string $migration
     * @return boolean
     */
    public function contains(string $migration): bool
    {
        return $this->migrationCache->rememberAll()
            ->contains(function ($item) use ($migration) {
                return $this->str->contains($item->migration, $migration);
            });
    }
}
