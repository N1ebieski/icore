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
use N1ebieski\ICore\Models\Post;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Crons\Sitemap\Builder\Builder;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Filesystem\Factory as Storage;

class PostBuilder extends Builder
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $route = 'web.post.show';

    /**
     * [protected description]
     * @var string
     */
    protected $path = 'vendor/icore/sitemap/posts';

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
        protected Post $post
    ) {
        parent::__construct($arrayToXml, $url, $carbon, $storage, $config, $collect);
    }

    /**
     * Undocumented function
     *
     * @param Closure $closure
     * @return bool
     */
    public function chunkCollection(Closure $closure): bool
    {
        return $this->post->makeRepo()->chunkActiveWithModelsCount($closure);
    }
}
