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

namespace N1ebieski\ICore\Utils\DOMDocument\Interfaces;

use DOMDocument;

interface DOMDocumentAdapterInterface
{
    /**
     * Undocumented function
     *
     * @param string $source
     * @param integer $options
     * @return DOMDocument|bool
     */
    public function loadHTML(string $source, int $options = 0);

    /**
     * Undocumented function
     *
     * @return string|false
     */
    public function saveHTML();
}
