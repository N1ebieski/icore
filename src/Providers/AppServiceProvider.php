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

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->scoped(\N1ebieski\ICore\Loads\ThemeLoad::class);

        $this->app->scoped(\N1ebieski\ICore\Loads\LangLoad::class);

        $this->app->scoped(\Torann\GeoIP\GeoIP::class, 'geoip');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $aliasLoader = \Illuminate\Foundation\AliasLoader::getInstance();

        $aliasLoader->alias('AutoTranslate', \N1ebieski\ICore\ValueObjects\AutoTranslate::class);
        $aliasLoader->alias('Lang', \N1ebieski\ICore\ValueObjects\Lang::class);
    }
}
