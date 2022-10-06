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

namespace N1ebieski\ICore\Crons\Sitemap\Builder;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Crons\Sitemap\Builder\Builder;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Filesystem\Factory as Storage;

class SitemapBuilder extends Builder
{
    /**
     * [public description]
     * @var string
     */
    public $path = 'vendor/icore/sitemap';

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
        protected Str $str
    ) {
        parent::__construct($arrayToXml, $url, $carbon, $storage, $config, $collect);
    }

    /**
     * Undocumented function
     *
     * @param Collect $collection
     * @return self
     */
    public function setCollection(Collect $collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function addToSitemap(): void
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
     * @param Closure $closure
     * @return bool
     */
    public function chunkCollection(Closure $closure): bool
    {
        $closure(
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
    public function putSitemap(): bool
    {
        return $this->storage->disk('public')->put($this->path . '/sitemap.xml', $this->contents);
    }
}
