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

namespace N1ebieski\ICore\Utils\Conversions;

use Closure;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Utils\Conversions\Interfaces\Handler;

class Replacement implements Handler
{
    /**
     * Undocumented function
     *
     * @param Collect $collect
     * @param array $replacement
     */
    public function __construct(
        protected Collect $collect,
        protected array $replacement
    ) {
        //
    }

    /**
     *
     * @param mixed $value
     * @param Closure $next
     * @return mixed
     */
    public function handle($value, Closure $next): mixed
    {
        $replacement = $this->collect->make($this->replacement);

        return $next(str_replace(
            $replacement->keys()->toArray(),
            $replacement->values()->toArray(),
            $value
        ));
    }
}
