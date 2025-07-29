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

use Illuminate\Support\Facades\Config;
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

        $this->app->bind(\N1ebieski\ICore\Http\Clients\AI\Interfaces\AIClientInterface::class, function (Application $app) {
            /** @var \N1ebieski\ICore\Http\Clients\AI\Factories\AIClientFactory $factory */
            $factory = $app->make(\N1ebieski\ICore\Http\Clients\AI\Factories\AIClientFactory::class, [
                'app' => $app
            ]);

            /** @var \N1ebieski\ICore\ValueObjects\AI\Driver $driver */
            $driver = Config::get('icore.ai.driver');

            return $factory->makeClient($driver);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
