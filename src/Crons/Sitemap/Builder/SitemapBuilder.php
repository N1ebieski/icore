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
    protected $priority = '0.8';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $changefreq = 'daily';

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
        parent::__construct($arrayToXml, $url, $carbon, $storage, $config, $collect);
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
                'changefreq' => $this->changefreq,
                'priority' => $this->priority
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
