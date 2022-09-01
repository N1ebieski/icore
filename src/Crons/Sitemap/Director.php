<?php

namespace N1ebieski\ICore\Crons\Sitemap;

use N1ebieski\ICore\Crons\Sitemap\Builder\Builder;

class Director
{
    /**
     * Undocumented function
     *
     * @return void
     */
    public function build(Builder $builder): void
    {
        $builder->chunkCollection(function ($collection) use ($builder) {
            $builder->setCollection($collection);

            $builder->addToSitemap();

            if ($builder->isMaxItems()) {
                $builder->prepareSitemapContents();

                $builder->putSitemap();

                $builder->nullSitemap();
            }
        });
    }
}
