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
use Illuminate\Support\Carbon;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Filesystem\Factory as Storage;

/**
 * @property string $route
 * @property string $changefreq
 * @property string $priority
 */
abstract class Builder
{
    /**
     * Undocumented variable
     *
     * @var Collect
     */
    protected $collection;

    /**
     * Undocumented variable
     *
     * @var Collect
     */
    protected $sitemap;

    /**
     *
     * @var string
     */
    protected $contents;

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
    protected $rootElementName = 'urlset';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $childElementName = 'url';

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $iterator = 0;

    /**
     *
     * @param ArrayToXml $arrayToXml
     * @param URL $url
     * @param Carbon $carbon
     * @param Storage $storage
     * @param Config $config
     * @param Collect $collect
     * @return void
     */
    public function __construct(
        protected ArrayToXml $arrayToXml,
        protected URL $url,
        protected Carbon $carbon,
        protected Storage $storage,
        protected Config $config,
        protected Collect $collect
    ) {
        $this->nullSitemap();
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
     * @param integer $count
     * @return integer
     */
    protected function countPages(int $count): int
    {
        $pages = (int)ceil($count / $this->config->get('database.paginate'));

        return $pages > 0 ? $pages : 1;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function addToSitemap(): void
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
    public function putSitemap(): bool
    {
        $this->iterator++;

        return $this->storage->disk('public')->put(
            $this->path . '/sitemap-' . $this->iterator . '.xml',
            $this->contents
        );
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function nullSitemap(): void
    {
        $this->sitemap = $this->collect->make([]);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function prepareSitemapContents(): void
    {
        $this->contents = $this->arrayToXml->convert(
            [
                $this->childElementName => $this->sitemap->toArray()
            ],
            [
                'rootElementName' => $this->rootElementName,
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
    public function isMaxItems(): bool
    {
        return $this->sitemap->count() >= $this->config->get('icore.sitemap.max_items')
            || $this->collection->count() < $this->config->get('icore.sitemap.limit');
    }

    abstract public function chunkCollection(Closure $closure): bool;
}
