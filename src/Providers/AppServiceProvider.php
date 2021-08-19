<?php

namespace N1ebieski\ICore\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * [AppServiceProvider description]
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\N1ebieski\ICore\Utils\FileUtil::class, function ($app, $with) {
            return new \N1ebieski\ICore\Utils\FileUtil(
                $app->make(\Illuminate\Contracts\Filesystem\Factory::class),
                $with['path'] ?? '',
                $with['file'] ?? null,
                $with['disk'] ?? 'public'
            );
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
