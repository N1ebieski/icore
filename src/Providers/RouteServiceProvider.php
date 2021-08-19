<?php

namespace N1ebieski\ICore\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'N1ebieski\ICore\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->app['router']->bind('post_cache', function ($value) {
            return $this->app->make(\N1ebieski\ICore\Cache\PostCache::class)->rememberBySlug($value)
                ?? $this->app->abort(404);
        });

        $this->app['router']->bind('page_cache', function ($value) {
            return $this->app->make(\N1ebieski\ICore\Cache\PageCache::class)->rememberBySlug($value)
                ?? $this->app->abort(404);
        });

        $this->app['router']->bind('category_post_cache', function ($value) {
            return $this->app->make(\N1ebieski\ICore\Models\Category\Post\Category::class)
                ->makeCache()->rememberBySlug($value) ?? $this->app->abort(404);
        });

        $this->app['router']->bind('tag_cache', function ($value) {
            return $this->app->make(\N1ebieski\ICore\Cache\TagCache::class)->rememberBySlug($value)
                ?? $this->app->abort(404);
        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapAuthRoutes();

        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapAdminRoutes();
    }

    /**
     * Auth routes
     *
     * @return void
     */
    protected function mapAuthRoutes()
    {
        $this->app['router']->middleware('icore.web')
            ->namespace($this->namespace)
            ->group(function ($router) {
                if (file_exists($override = base_path('routes') . '/vendor/icore/auth.php')) {
                    require($override);
                } else {
                    require(__DIR__ . '/../../routes/auth.php');
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
        $this->app['router']->middleware(['icore.web', 'icore.force.verified'])
            ->as('web.')
            ->namespace($this->namespace.'\Web')
            ->group(function ($router) {
                foreach (glob(__DIR__ . '/../../routes/web/*.php') as $filename) {
                    if (file_exists($override = base_path('routes') . '/vendor/icore/web/' . basename($filename))) {
                        require($override);
                    } else {
                        require($filename);
                    }
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
        $this->app['router']->middleware([
                \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
                'icore.api',
                'icore.force.verified'
            ])
            ->prefix('api')
            ->as('api.')
            ->namespace($this->namespace . '\Api')
            ->group(function ($router) {
                foreach (glob(__DIR__ . '/../../routes/api/*.php') as $filename) {
                    if (file_exists($override = base_path('routes') . '/vendor/icore/api/' . basename($filename))) {
                        require($override);
                    } else {
                        require($filename);
                    }
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
        $this->app['router']->middleware([
                'icore.web',
                'auth',
                'verified',
                'permission:admin.access'
            ])
            ->prefix('admin')
            ->as('admin.')
            ->namespace($this->namespace.'\Admin')
            ->group(function ($router) {
                foreach (glob(__DIR__ . '/../../routes/admin/*.php') as $filename) {
                    if (file_exists($override = base_path('routes') . '/vendor/icore/admin/' . basename($filename))) {
                        require($override);
                    } else {
                        require($filename);
                    }
                }
            });
    }
}
