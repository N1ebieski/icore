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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    // /**
    //  * This namespace is applied to your controller routes.
    //  *
    //  * In addition, it is set as the URL generator's root namespace.
    //  *
    //  * @var string
    //  */
    // protected $namespace = 'N1ebieski\ICore\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        Route::fallback(function (Request $request) {
            $url = URL::full();

            if (!Str::match('/\/([a-z]{2})\//', $url)) {
                $parsed = parse_url($url);
                $parsed['path'] = '/pl' . ($parsed['path'] ?? null);

                return Response::redirectTo(Str::buildUrl($parsed), HttpResponse::HTTP_MOVED_PERMANENTLY);
            }

            return App::abort(HttpResponse::HTTP_NOT_FOUND);
        });

        Route::bind('post_cache', function (string $value) {
            return $this->app->make(\N1ebieski\ICore\Cache\Post\PostCache::class)->rememberBySlug($value)
                ?? $this->app->abort(HttpResponse::HTTP_NOT_FOUND);
        });

        Route::bind('page_cache', function (string $value) {
            return $this->app->make(\N1ebieski\ICore\Cache\Page\PageCache::class)->rememberBySlug($value)
                ?? $this->app->abort(HttpResponse::HTTP_NOT_FOUND);
        });

        Route::bind('category_post_cache', function (string $value) {
            return $this->app->make(\N1ebieski\ICore\Models\Category\Post\Category::class)
                ->makeCache()->rememberBySlug($value) ?? $this->app->abort(HttpResponse::HTTP_NOT_FOUND);
        });

        Route::bind('tag_cache', function (string $value) {
            return $this->app->make(\N1ebieski\ICore\Cache\Tag\TagCache::class)->rememberBySlug($value)
                ?? $this->app->abort(HttpResponse::HTTP_NOT_FOUND);
        });

        $this->routes(function () {
            $this->mapApiRoutes();

            $this->mapAdminRoutes();

            $this->mapAuthRoutes();

            $this->mapWebRoutes();
        });
    }

    /**
     * Auth routes
     *
     * @return void
     */
    protected function mapAuthRoutes()
    {
        if (Config::get('icore.routes.auth.enabled') === false) {
            return;
        }

        $router = Route::middleware('icore.web')
            ->prefix(Config::get('icore.routes.auth.prefix'));

        $router->group(function () {
            if (!file_exists(base_path('routes') . '/vendor/icore/auth.php')) {
                require(__DIR__ . '/../../routes/auth.php');
            }
        });

        $router->namespace(Config::get('icore.routes.auth.namespace', $this->namespace))
            ->group(function () {
                if (file_exists($filename = base_path('routes') . '/vendor/icore/auth.php')) {
                    require($filename);
                }
            });
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        if (Config::get('icore.routes.web.enabled') === false) {
            return;
        }

        $router = Route::middleware(['icore.web', 'icore.force.verified'])
            // ->prefix(Config::get('icore.routes.web.prefix') . '{lang}')
            // ->where(['lang' => '[a-z]{2}'])
            ->as('web.');

        $router->group(function () {
            $filenames = glob(__DIR__ . '/../../routes/web/*.php') ?: [];

            foreach ($filenames as $filename) {
                if (!file_exists(base_path('routes') . '/vendor/icore/web/' . basename($filename))) {
                    require($filename);
                }
            }
        });

        $router->namespace(Config::get('icore.routes.web.namespace', $this->namespace . '\Web'))
            ->group(function () {
                $filenames = glob(base_path('routes') . '/vendor/icore/web/*.php') ?: [];

                foreach ($filenames as $filename) {
                    require($filename);
                }
            });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        if (Config::get('icore.routes.api.enabled') === false) {
            return;
        }

        $router = Route::middleware(['icore.api', 'icore.force.verified'])
            ->prefix(Config::get('icore.routes.api.prefix', 'api'))
            ->as('api.');

        $router->group(function () {
            $filenames = glob(__DIR__ . '/../../routes/api/*.php') ?: [];

            foreach ($filenames as $filename) {
                if (!file_exists(base_path('routes') . '/vendor/icore/api/' . basename($filename))) {
                    require($filename);
                }
            }
        });

        $router->namespace(Config::get('icore.routes.api.namespace', $this->namespace . '\Api'))
            ->group(function () {
                $filenames = glob(base_path('routes') . '/vendor/icore/api/*.php') ?: [];

                foreach ($filenames as $filename) {
                    require($filename);
                }
            });
    }

    /**
     * Define the "admin" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapAdminRoutes()
    {
        if (Config::get('icore.routes.admin.enabled') === false) {
            return;
        }

        $router = Route::middleware([
                'icore.web',
                'auth',
                'verified',
                'permission:admin.access'
            ])
            ->prefix(Config::get('icore.routes.admin.prefix', 'admin'))
            ->as('admin.');

        $router->group(function () {
            $filenames = glob(__DIR__ . '/../../routes/admin/*.php') ?: [];

            foreach ($filenames as $filename) {
                if (!file_exists(base_path('routes') . '/vendor/icore/admin/' . basename($filename))) {
                    require($filename);
                }
            }
        });

        $router->namespace(Config::get('icore.routes.admin.namespace', $this->namespace . '\Admin'))
            ->group(function () {
                $filenames = glob(base_path('routes') . '/vendor/icore/admin/*.php') ?: [];

                foreach ($filenames as $filename) {
                    require($filename);
                }
            });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
