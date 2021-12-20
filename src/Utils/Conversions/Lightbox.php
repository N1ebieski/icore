<?php

namespace N1ebieski\ICore\Utils\Conversions;

use Closure;
use Illuminate\Support\Str;
use N1ebieski\ICore\Utils\DOMDocumentAdapter;
use N1ebieski\ICore\Utils\Conversions\Interfaces\Handler;

class Lightbox implements Handler
{
    /**
     * Undocumented variable
     *
     * @var DOMDocumentAdapter
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
     * @param DOMDocumentAdapter $dom
     * @param Str $str
     */
    public function __construct(DOMDocumentAdapter $dom, Str $str)
    {
        $this->dom = $dom;

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
        $dom = $this->dom->loadHTML($value);

        $galleryId = (string)$this->str->uuid();

        foreach ($dom->getElementsByTagName('img') as $img) {
            $imgSrc = $img->attributes->getNamedItem('src')->nodeValue;
            $imgAlt = $img->attributes->getNamedItem('alt')->nodeValue;

            $img->setAttribute('data-src', $imgSrc);
            $img->setAttribute('class', 'img-fluid lazy');
            $img->setAttribute('src', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');

            $a = $dom->createElement('a');

            $a->setAttribute('class', 'lightbox');
            $a->setAttribute('data-gallery', $galleryId);
            $a->setAttribute('href', $imgSrc);
            $a->setAttribute('title', $imgAlt);

            $img->parentNode->replaceChild($a, $img);

            $a->appendChild($img);
        }

        return $next($this->dom->saveHtml());
    }
}
