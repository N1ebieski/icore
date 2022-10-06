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

namespace N1ebieski\ICore\Crons\Sitemap;

use N1ebieski\ICore\Crons\Sitemap\Director;
use N1ebieski\ICore\Crons\Sitemap\Builder\Builder;
use Illuminate\Contracts\Container\Container as App;

class SitemapCron
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $builders = [
        \N1ebieski\ICore\Crons\Sitemap\Builder\PostBuilder::class,
        \N1ebieski\ICore\Crons\Sitemap\Builder\PageBuilder::class,
        \N1ebieski\ICore\Crons\Sitemap\Builder\Category\Post\CategoryBuilder::class,
        \N1ebieski\ICore\Crons\Sitemap\Builder\SitemapBuilder::class
    ];

    /**
     * Undocumented function
     *
     * @param App $app
     */
    public function __construct(protected App $app)
    {
        //
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function __invoke(): void
    {
        /** @var Director */
        $director = $this->app->make(Director::class);

        foreach ($this->builders as $builder) {
            /** @var Builder */
            $builder = $this->app->make($builder);

            $director->build($builder);
        }
    }
}
