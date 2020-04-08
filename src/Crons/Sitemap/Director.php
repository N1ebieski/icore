<?php

namespace N1ebieski\ICore\Crons\Sitemap;

use N1ebieski\ICore\Crons\Sitemap\Builder\Builder;

class Director
{
    /**
     * Undocumented variable
     *
     * @var Builder
     */
    protected $builder;

    /**
     * Undocumented function
     *
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function build() : void
    {
        $this->builder->chunkCollection(function ($collection) {
            $this->builder->setCollection($collection);

            $this->builder->addToSitemap();

            if ($this->builder->isMaxItems()) {
                $this->builder->prepareSitemap();
    
                $this->builder->putSitemap();

                $this->builder->nullSitemap();
            }
        });
    }
}
