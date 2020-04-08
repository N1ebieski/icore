<?php

namespace N1ebieski\ICore\Crons\Sitemap\Builder;

use Closure;
use Illuminate\Support\Carbon;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Filesystem\Factory as Storage;

abstract class Builder
{
    /**
     * Undocumented variable
     *
     * @var ArrayToXml
     */
    protected $arrayToXml;

    /**
     * Undocumented variable
     *
     * @var URL
     */
    protected $url;

    /**
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * Undocumented variable
     *
     * @var Storage
     */
    protected $storage;

    /**
     * Undocumented variable
     *
     * @var Collect
     */
    protected $collect;

    /**
     * Undocumented variable
     *
     * @var Collection
     */
    protected $collection;

    /**
     * Undocumented variable
     *
     * @var Collect|string
     */
    protected $sitemap;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected int $maxItems = 10000;

    /**
     * [protected description]
     * @var string
     */
    protected string $path = 'vendor/icore/sitemap';

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected int $iterator = 0;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $paginate;

    /**
     * Undocumented function
     *
     * @param ArrayToXml $arrayToXml
     * @param URL $url
     * @param Carbon $carbon
     * @param Storage $storage
     * @param Config $config
     * @param Collect $collect
     */
    public function __construct(
        ArrayToXml $arrayToXml,
        URL $url,
        Carbon $carbon,
        Storage $storage,
        Config $config,
        Collect $collect
    ) {
        $this->arrayToXml = $arrayToXml;
        $this->url = $url;
        $this->carbon = $carbon;
        $this->storage = $storage;
        $this->collect = $collect;

        $this->paginate = $config->get('database.paginate');
        $this->nullSitemap();
    }

    /**
     * Undocumented function
     *
     * @param Collection $collection
     * @return void
     */
    public function setCollection(Collection $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $count
     * @return integer
     */
    protected function countPages(int $count) : int
    {
        $pages = ceil($count/$this->paginate);

        return $pages > 0 ? $pages : 1;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function addToSitemap() : void
    {
        $this->collection->each(function ($item) {
            for ($i = 1; $i <= $this->countPages($item->models_count); $i++) {
                $this->sitemap->push([
                    'loc' => $this->url->route($this->route, [
                        $item->slug,
                        'page' => ($i > 1 ? $i : null)
                    ]),
                    'lastmod' => $this->carbon->parse($item->updated_at)->format('Y-m-d'),
                    'changefreq' => $this->changefreq,
                    'priority' => $this->priority
                ]);
            }
        });
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function putSitemap() : bool
    {
        $this->iterator++;

        return $this->storage->disk('public')->put($this->path . '/sitemap-' . $this->iterator .'.xml', $this->sitemap);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function nullSitemap() : void
    {
        $this->sitemap = $this->collect->make([]);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function prepareSitemap() : string
    {
        return $this->sitemap = $this->arrayToXml->convert(
            [
                'url' => $this->sitemap->toArray()
            ],
            [
                'rootElementName' => 'urlset',
                '_attributes' => [
                    'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9'
                ],
            ],
            true,
            'UTF-8'
        );
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isMaxItems() : bool
    {
        return $this->sitemap->count() >= $this->maxItems || $this->collection->count() < 1000;
    }

    abstract public function chunkCollection(Closure $callback) : bool;
}
