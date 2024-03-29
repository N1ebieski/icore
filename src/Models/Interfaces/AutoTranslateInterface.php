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

use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\ValueObjects\AutoTranslate;
use Illuminate\Database\Eloquent\Relations\HasMany;
use N1ebieski\ICore\Services\Interfaces\UpdateServiceInterface;

/**
 * @property AutoTranslate $auto_translate
 * @property-read \Illuminate\Database\Eloquent\Collection|Model[] $langs
 */
interface AutoTranslateInterface
{
    /**
     *
     * @return HasMany
     */
    public function langs(): HasMany;

    /**
     *
     * @return UpdateServiceInterface
     */
    public function makeService(): UpdateServiceInterface;
}
