<?php

namespace N1ebieski\ICore\Utils\Conversions;

use Closure;
use DOMDocument;
use Illuminate\Support\Str;
use N1ebieski\ICore\Utils\Conversions\Interfaces\Handler;

class Lightbox implements Handler
{
    /**
     * Undocumented variable
     *
     * @var DomDocument
     */
    private $dom;

    /**
     * Undocumented variable
     *
     * @var Str
     */
    private $str;

    /**
     * Undocumented function
     *
     * @param Str $str
     */
    public function __construct(Str $str)
    {
        $this->dom = new DOMDocument();

        $this->str = $str;
    }

    /**
     * Undocumented function
     *
     * @param [type] $value
     * @param Closure $next
     * @return void
     */
    public function handle($value, Closure $next)
    {
        $this->dom->loadHTML(
            mb_convert_encoding($value, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        $galleryId = (string)$this->str->uuid();

        foreach ($this->dom->getElementsByTagName('img') as $img) {
            $imgSrc = $img->attributes->getNamedItem('src')->nodeValue;
            $imgAlt = $img->attributes->getNamedItem('alt')->nodeValue;

            $img->setAttribute('data-src', $imgSrc);
            $img->setAttribute('class', 'img-fluid lazy');
            $img->setAttribute('src', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');

            $a = $this->dom->createElement('a');

            $a->setAttribute('class', 'lightbox');
            $a->setAttribute('data-gallery', $galleryId);
            $a->setAttribute('href', $imgSrc);
            $a->setAttribute('title', $imgAlt);

            $img->parentNode->replaceChild($a, $img);

            $a->appendChild($img);
        }

        return $next(mb_convert_encoding($this->dom->saveHtml(), 'UTF-8', 'HTML-ENTITIES'));
    }
}
