<?php

namespace N1ebieski\ICore\Crons\Sitemap\Builder;

use Closure;
use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Page\Page;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Crons\Sitemap\Builder\Builder;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as Storage;

class PageBuilder extends Builder
{
    /**
     * Undocumented variable
     *
     * @var Page
     */
    protected $page;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $route = 'web.page.show';

    /**
     * [protected description]
     * @var string
     */
    protected $path = 'vendor/icore/sitemap/pages';

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
     * @param Page $page
     */
    public function __construct(
        ArrayToXml $arrayToXml,
        URL $url,
        Carbon $carbon,
        Storage $storage,
        Config $config,
        Collect $collect,
        Page $page
    ) {
        parent::__construct($arrayToXml, $url, $carbon, $storage, $config, $collect);

        $this->page = $page;
    }

    /**
     * Undocumented function
     *
     * @param Closure $callback
     * @return void
     */
    public function chunkCollection(Closure $callback) : bool
    {
        return $this->page->makeRepo()->chunkActiveWithModelsCount($callback);
    }
}
