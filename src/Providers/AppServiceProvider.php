<?php

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
        $this->app->bind(\GuzzleHttp\ClientInterface::class, \GuzzleHttp\Client::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $aliasLoader = \Illuminate\Foundation\AliasLoader::getInstance();

        foreach (glob(__DIR__ . '/../ValueObjects/**/*.php') as $classPath) {
            if (!preg_match('/ValueObjects\/([A-Za-z\/]+).php/', $classPath, $matches)) {
                continue;
            }

            $alias = str_replace('/', '\\', $matches[1]);

            $aliasLoader->alias($alias, 'N1ebieski\\ICore\\ValueObjects\\' . $alias);
        }
    }
}
