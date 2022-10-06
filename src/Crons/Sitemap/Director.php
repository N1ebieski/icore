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
