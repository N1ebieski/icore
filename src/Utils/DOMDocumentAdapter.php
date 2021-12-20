<?php

namespace N1ebieski\ICore\Utils;

use DOMNode;
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
        $this->decorated->loadHTML(
            mb_convert_encoding($source, 'HTML-ENTITIES', 'UTF-8'),
            $options
        );

        return $this->decorated;
    }

    /**
     * Undocumented function
     *
     * @param DOMNode|null $node
     * @return string|false
     */
    public function saveHTML(DOMNode $node = null)
    {
        # remove <!DOCTYPE
        $this->decorated->removeChild($this->decorated->doctype);

        # remove <html><body></body></html>
        $this->decorated->replaceChild(
            $this->decorated->firstChild->firstChild->firstChild,
            $this->decorated->firstChild
        );

        return mb_convert_encoding(
            $this->decorated->saveHtml($node),
            'UTF-8',
            'HTML-ENTITIES'
        );
    }
}
