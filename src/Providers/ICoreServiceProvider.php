<?php

namespace N1ebieski\ICore\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * [ICoreServiceProvider description]
 */
class ICoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.  '/../../config/icore.php', 'icore');

        $this->app->register(\Unikent\Cache\TaggableFileCacheServiceProvider::class);
        $this->app->register(\Spatie\Permission\PermissionServiceProvider::class);

        $this->app->register(ConfigServiceProvider::class);
        $this->app->register(AppServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(MacroServiceProvider::class);
        $this->app->register(ViewServiceProvider::class);
        $this->app->register(ScheduleServiceProvider::class);

        $this->app['router']->middlewareGroup('icore.web', [
            'throttle:60,1',
            \N1ebieski\ICore\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \N1ebieski\ICore\Http\Middleware\XSSProtection::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \Nckg\Minify\Middleware\MinifyResponse::class
        ]);

        $this->app['router']->aliasMiddleware('permission', \Spatie\Permission\Middlewares\PermissionMiddleware::class);
        $this->app['router']->aliasMiddleware('role', \Spatie\Permission\Middlewares\RoleMiddleware::class);
        $this->app['router']->aliasMiddleware('icore.ban.user', \N1ebieski\ICore\Http\Middleware\BanUser::class);
        $this->app['router']->aliasMiddleware('icore.ban.ip', \N1ebieski\ICore\Http\Middleware\BanIp::class);
        $this->app['router']->aliasMiddleware('icore.force.verified', \N1ebieski\ICore\Http\Middleware\VerifyEmail::class);
        $this->app['router']->aliasMiddleware('icore.guest', \N1ebieski\ICore\Http\Middleware\RedirectIfAuthenticated::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/icore.php' => config_path('icore.php'),
            // __DIR__ . '/../../config/jsvalidation.php' => config_path('jsvalidation.php'),
            // __DIR__ . '/../../config/logic_captcha.php' => config_path('logic_captcha.php'),
            // __DIR__ . '/../../config/permission.php' => config_path('permission.php'),
            // __DIR__ . '/../../config/purifier.php' => config_path('purifier.php'),
            // __DIR__ . '/../../config/sluggable.php' => config_path('sluggable.php'),
            // __DIR__ . '/../../config/taggable.php' => config_path('taggable.php'),
            // __DIR__ . '/../../config/view-components.php' => config_path('view-components.php'),
        ]);

        $this->publishes([
            __DIR__.'/../../routes/web' => base_path('routes') . '/vendor/icore/web'
        ], 'icore.routes.web');

        $this->publishes([
            __DIR__.'/../../routes/admin' => base_path('routes') . '/vendor/icore/admin'
        ], 'icore.routes.admin');

        $this->publishes([
            __DIR__.'/../../routes/auth.php' => base_path('routes') . '/vendor/icore/auth.php'
        ], 'icore.routes.auth');

        // $this->publishes([
        //     __DIR__.'/../../tests/Feature' => base_path('tests/Feature') . '/vendor/icore'
        // ], 'icore.tests');

        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'icore');

        $this->publishes([
            __DIR__ . '/../../resources/lang/en' => resource_path('lang/vendor/icore/en'),
            __DIR__ . '/../../resources/lang/pl' => resource_path('lang/vendor/icore/pl'),
            __DIR__ . '/../../resources/lang/vendor/laravel' => resource_path('lang'),
        ], 'icore.lang');

        $this->publishes([
            __DIR__ . '/../../resources/js' => resource_path('js/vendor/icore'),
        ], 'icore.js');

        $this->publishes([
            __DIR__ . '/../../resources/sass' => resource_path('sass/vendor/icore'),
        ], 'icore.sass');

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'icore');

        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/icore'),
        ], 'icore.views');

        $this->publishes([
            __DIR__ . '/../../public/css' => public_path('css/vendor/icore'),
            __DIR__ . '/../../public/img' => public_path('img/vendor/icore'),
            __DIR__ . '/../../public/js' => public_path('js/vendor/icore'),
            __DIR__ . '/../../public/svg' => public_path('svg/vendor/icore'),
            __DIR__ . '/../../public/fonts/vendor' => public_path('fonts/vendor'),
            __DIR__ . '/../../public/mix-manifest.json' => public_path('mix-manifest.json')
        ], 'icore.public');

        $this->app->make('Illuminate\Database\Eloquent\Factory')->load(base_path('database/factories') . '/vendor/icore');

        $this->publishes([
            __DIR__ . '/../../database/factories' => base_path('database/factories') . '/vendor/icore',
        ], 'icore.factories');

        // Migrations load by php artisan migrate:fresh --path="database/migrations/vendor/icore"

        $this->publishes([
            __DIR__ . '/../../database/migrations' => base_path('database/migrations') . '/vendor/icore',
        ], 'icore.migrations');

        $this->publishes([
            __DIR__ . '/../../database/seeds' => base_path('database/seeds') . '/vendor/icore',
        ], 'icore.seeds');
    }
}
