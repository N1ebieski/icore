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

namespace N1ebieski\ICore\Jobs\AutoTranslate\Data\CategoryLang;

use N1ebieski\ICore\Models\CategoryLang\CategoryLang;
use N1ebieski\ICore\Jobs\AutoTranslate\Data\Interfaces\OutputDataInterface;

class CategoryLangOutputData implements OutputDataInterface
{
    /**
     *
     * @param CategoryLang $categoryLang
     * @return void
     */
    public function __construct(protected CategoryLang $categoryLang,)
    {
        //
    }

    /**
     *
     * @param array $attributes
     * @return array
     */
    public function getOutput(array $attributes): array
    {
        return $attributes;
    }
}
