<?php

namespace N1ebieski\ICore\Utils;

use DOMDocument;

class DOMDocumentAdapter
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
        $this->decorated->removeChild($this->decorated->doctype);

        // remove <html><body></body></html>
        return substr(trim($this->decorated->saveHtml($this->decorated->documentElement)), 12, -14);
    }
}
