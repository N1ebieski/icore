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

namespace N1ebieski\ICore\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        /** @var \N1ebieski\ICore\Macros\Collection\Paginate */
        $paginate = $this->app->make(\N1ebieski\ICore\Macros\Collection\Paginate::class);

        Collection::macro('paginate', $paginate());

        /** @var \N1ebieski\ICore\Macros\Collection\FlattenRelation */
        $flattenRelation = $this->app->make(\N1ebieski\ICore\Macros\Collection\FlattenRelation::class);

        Collection::macro('flattenRelation', $flattenRelation());

        /** @var \N1ebieski\ICore\Macros\Collection\IsEmptyItems */
        $isEmptyItems = $this->app->make(\N1ebieski\ICore\Macros\Collection\IsEmptyItems::class);

        Collection::macro('isEmptyItems', $isEmptyItems());

        /** @var \N1ebieski\ICore\Macros\Collection\IsNullItems */
        $isNullItems = $this->app->make(\N1ebieski\ICore\Macros\Collection\IsNullItems::class);

        Collection::macro('isNullItems', $isNullItems());

        /** @var \N1ebieski\ICore\Macros\Str\RandomColor */
        $randomColor = $this->app->make(\N1ebieski\ICore\Macros\Str\RandomColor::class);

        Str::macro('randomColor', $randomColor());

        /** @var \N1ebieski\ICore\Macros\Str\Escaped */
        $escaped = $this->app->make(\N1ebieski\ICore\Macros\Str\Escaped::class);

        Str::macro('escaped', $escaped());

        /** @var \N1ebieski\ICore\Macros\Str\BuildUrl */
        $buildUrl = $this->app->make(\N1ebieski\ICore\Macros\Str\BuildUrl::class);

        Str::macro('buildUrl', $buildUrl());
    }
}
