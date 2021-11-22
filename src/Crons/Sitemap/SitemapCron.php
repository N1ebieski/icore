<?php

namespace N1ebieski\ICore\Crons\Sitemap;

use N1ebieski\ICore\Crons\Sitemap\Director;
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
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented function
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function __invoke(): void
    {
        foreach ($this->builders as $builder) {
            $this->app->make(Director::class, [
                'builder' => $this->app->make($builder)
            ])
            ->build();
        }
    }
}
