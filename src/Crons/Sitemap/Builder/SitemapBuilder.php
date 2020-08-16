<?php

namespace N1ebieski\ICore\Crons\Sitemap\Builder;

use Closure;
use Illuminate\Support\Carbon;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Crons\Sitemap\Builder\Builder;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Illuminate\Support\Str;

class SitemapBuilder extends Builder
{
    /**
     * [protected description]
     * @var string
     */
    protected $path = 'vendor/icore/sitemap';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $rootElementName = 'sitemapindex';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $childElementName = 'sitemap';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $priority = '0.8';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $changefreq = 'daily';

    /**
     * Undocumented variable
     *
     * @var Str
     */
    protected $str;

    /**
     * Undocumented function
     *
     * @param ArrayToXml $arrayToXml
     * @param URL $url
     * @param Carbon $carbon
     * @param Storage $storage
     * @param Config $config
     * @param Collect $collect
     * @param Str $str
     */
    public function __construct(
        ArrayToXml $arrayToXml,
        URL $url,
        Carbon $carbon,
        Storage $storage,
        Config $config,
        Collect $collect,
        Str $str
    ) {
        parent::__construct($arrayToXml, $url, $carbon, $storage, $config, $collect);

        $this->str = $str;
    }

    /**
     * Undocumented function
     *
     * @param Collect $collection
     * @return void
     */
    public function setCollection(Collect $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function addToSitemap() : void
    {
        $this->collection->each(function ($item) {
            $this->sitemap->push([
                'loc' => $this->storage->disk('public')->url($item->slug),
                'lastmod' => $this->carbon->createFromTimestamp($item->updated_at)->format('Y-m-d'),
            ]);
        });
    }

    /**
     * Undocumented function
     *
     * @param Closure $callback
     * @return void
     */
    public function chunkCollection(Closure $callback) : bool
    {
        $callback(
            $this->collect->make($this->storage->disk('public')->allFiles($this->path))
                ->map(function ($item) {
                    return (object)[
                        'slug' => $item,
                        'updated_at' => $this->storage->disk('public')->lastModified($item)
                    ];
                })
                ->filter(function ($item) {
                    return !$this->str->contains($item->slug, 'sitemap/sitemap.xml');
                })
        );

        return true;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function putSitemap() : bool
    {
        return $this->storage->disk('public')->put($this->path . '/sitemap.xml', $this->sitemap);
    }
}
