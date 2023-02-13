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

namespace N1ebieski\ICore\Models\Interfaces;

use N1ebieski\ICore\Services\Interfaces\LangServiceInterface;

/**
 * @property \N1ebieski\ICore\ValueObjects\Lang $lang
 * @property \N1ebieski\ICore\ValueObjects\Progress $progress
 * @property \Illuminate\Support\Carbon|null $translated_at
 * @property-read array<string> $transable
 */
interface TransableInterface
{
    /**
     *
     * @return array<string>
     */
    public function getTransable(): array;

    /**
     *
     * @return LangServiceInterface
     */
    public function makeService(): LangServiceInterface;
}
