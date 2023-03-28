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

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Support\ServiceProvider;

class ICoreServiceProvider extends ServiceProvider
{
    /**
     * [public description]
     * @var  string
     */
    public const VERSION = "8.2.13";

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ .  '/../../config/icore.php', 'icore');

        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'icore');

        $this->app->register(\Unikent\Cache\TaggableFileCacheServiceProvider::class);
        $this->app->register(\Spatie\Permission\PermissionServiceProvider::class);

        // @phpstan-ignore-next-line
        $this->app->register(LicenseServiceProvider::class);
        $this->app->register(AppServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(MacroServiceProvider::class);
        $this->app->register(ViewServiceProvider::class);
        $this->app->register(ScheduleServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);

        Route::middlewareGroup('icore.web', [
            \N1ebieski\ICore\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \N1ebieski\ICore\Http\Middleware\MultiLang::class,
            \N1ebieski\ICore\Http\Middleware\SetMultiLangCookie::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \N1ebieski\ICore\Http\Middleware\XSSProtection::class,
            \N1ebieski\ICore\Http\Middleware\TrimStrings::class,
            \N1ebieski\ICore\Http\Middleware\ClearWhitespacesInStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \Nckg\Minify\Middleware\MinifyResponse::class
        ]);

        Route::middlewareGroup('icore.api', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \N1ebieski\ICore\Http\Middleware\XSSProtection::class,
            \N1ebieski\ICore\Http\Middleware\TrimStrings::class,
            \N1ebieski\ICore\Http\Middleware\ClearWhitespacesInStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \N1ebieski\ICore\Http\Middleware\OptionalAuthSanctum::class,
            \N1ebieski\ICore\Http\Middleware\MultiLang::class
        ]);

        Route::aliasMiddleware('abilities', \Laravel\Sanctum\Http\Middleware\CheckAbilities::class);
        Route::aliasMiddleware('ability', \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class);
        Route::aliasMiddleware('permission', \Spatie\Permission\Middlewares\PermissionMiddleware::class);
        Route::aliasMiddleware('role', \Spatie\Permission\Middlewares\RoleMiddleware::class);
        Route::aliasMiddleware('icore.ban.user', \N1ebieski\ICore\Http\Middleware\BanUser::class);
        Route::aliasMiddleware('icore.ban.ip', \N1ebieski\ICore\Http\Middleware\BanIp::class);
        Route::aliasMiddleware('icore.force.verified', \N1ebieski\ICore\Http\Middleware\VerifyEmail::class);
        Route::aliasMiddleware('icore.guest', \N1ebieski\ICore\Http\Middleware\RedirectIfAuthenticated::class);
        Route::aliasMiddleware('icore.migration', \N1ebieski\ICore\Http\Middleware\CheckMigration::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'icore');

        $this->publishes([
            __DIR__ . '/../../config/icore.php' => config_path('icore.php'),
        ], 'icore.config');

        $this->publishes([
            __DIR__ . '/../../routes/web' => base_path('routes') . '/vendor/icore/web'
        ], 'icore.routes.web');

        $this->publishes([
            __DIR__ . '/../../routes/api' => base_path('routes') . '/vendor/icore/api'
        ], 'icore.routes.api');

        $this->publishes([
            __DIR__ . '/../../routes/admin' => base_path('routes') . '/vendor/icore/admin'
        ], 'icore.routes.admin');

        $this->publishes([
            __DIR__ . '/../../routes/auth.php' => base_path('routes') . '/vendor/icore/auth.php'
        ], 'icore.routes.auth');

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

        $this->publishes([
            __DIR__ . '/../../resources/views/admin' => resource_path('views/vendor/icore/admin'),
        ], 'icore.views.admin');

        $this->publishes([
            __DIR__ . '/../../resources/views/auth' => resource_path('views/vendor/icore/auth'),
            __DIR__ . '/../../resources/views/mails' => resource_path('views/vendor/icore/mails'),
            __DIR__ . '/../../resources/views/web' => resource_path('views/vendor/icore/web')
        ], 'icore.views.web');

        $this->publishes([
            __DIR__ . '/../../public/docs' => public_path('docs'),
        ], 'icore.public.docs');

        $this->publishes([
            __DIR__ . '/../../public/css' => public_path('css/vendor/icore'),
            __DIR__ . '/../../public/mix-manifest.json' => public_path('mix-manifest.json')
        ], 'icore.public.css');

        $this->publishes([
            __DIR__ . '/../../public/images' => public_path('images/vendor/icore'),
            __DIR__ . '/../../public/fonts/vendor' => public_path('fonts/vendor'),
        ], 'icore.public.images');

        $this->publishes([
            __DIR__ . '/../../public/js' => public_path('js/vendor/icore'),
            __DIR__ . '/../../public/mix-manifest.json' => public_path('mix-manifest.json')
        ], 'icore.public.js');

        $this->publishes([
            __DIR__ . '/../../database/factories' => base_path('database/factories') . '/vendor/icore',
        ], 'icore.factories');

        $this->publishes([
            __DIR__ . '/../../database/migrations' => base_path('database/migrations') . '/vendor/icore',
        ], 'icore.migrations');

        $this->publishes([
            __DIR__ . '/../../database/seeders' => base_path('database/seeders') . '/vendor/icore',
        ], 'icore.seeders');
    }
}
