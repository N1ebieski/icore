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

namespace N1ebieski\ICore\Utils\DOMDocument;

use DOMDocument;
use N1ebieski\ICore\Utils\DOMDocument\Interfaces\DOMDocumentAdapterInterface;

class DOMDocumentAdapter implements DOMDocumentAdapterInterface
{
    /**
     * Undocumented variable
     *
     * @var DOMDocument
     */
    protected $decorated;

    /**
     * Undocumented function
     */
    public function __construct()
    {
        $this->decorated = new DOMDocument();
    }

    /**
     * Undocumented function
     *
     * @param string $source
     * @param integer $options
     * @return DOMDocument|bool
     */
    public function loadHTML(string $source, int $options = 0)
    {
        $this->decorated->loadHTML('<?xml encoding="utf-8"?>' . $source, $options);
        $this->decorated->encoding = 'utf-8';

        return $this->decorated;
    }

    /**
     * Undocumented function
     *
     * @return string|false
     */
    public function saveHTML()
    {
        // remove <!DOCTYPE
        // @phpstan-ignore-next-line
        $this->decorated->removeChild($this->decorated->doctype);

        // remove <html><body></body></html>
        return substr(trim($this->decorated->saveHtml($this->decorated->documentElement) ?: ''), 12, -14);
    }
}
