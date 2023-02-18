<?php

namespace N1ebieski\ICore\Utils\Conversions;

use Closure;
use DOMDocument;
use DOMException;
use Illuminate\Support\Str;
use N1ebieski\ICore\Utils\Conversions\Interfaces\Handler;
use N1ebieski\ICore\Utils\DOMDocument\Interfaces\DOMDocumentAdapterInterface;

class Lightbox implements Handler
{
    /**
     * Undocumented function
     *
     * @param DOMDocumentAdapterInterface $dom
     * @param Str $str
     */
    public function __construct(
        protected DOMDocumentAdapterInterface $dom,
        protected Str $str
    ) {
        //
    }

    /**
     *
     * @param mixed $value
     * @param Closure $next
     * @return mixed
     * @throws DOMException
     */
    public function handle($value, Closure $next): mixed
    {
        /** @var DOMDocument */
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
