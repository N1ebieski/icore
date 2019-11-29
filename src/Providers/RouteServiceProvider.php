<?php

namespace N1ebieski\ICore\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Support\Facades\App;

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
        //
        parent::boot();

        $this->app['router']->bind('mailing_inactive', function ($value) {
            return \N1ebieski\ICore\Models\Mailing::where([
                    ['status', '!=', 1],
                    ['id', $value]
                ])->firstOrFail();
        });

        $this->app['router']->bind('post_active', function ($value) {
            return Post::where([
                    ['status', 1],
                    ['published_at', '!=', null],
                    ['id', $value]
                ])->firstOrFail();
        });

        $this->app['router']->bind('post_cache', function($value) {
            return App::make(\N1ebieski\ICore\Cache\PostCache::class)->rememberBySlug($value)
                ?? abort(404);
        });

        $this->app['router']->bind('page_cache', function($value) {
            return App::make(\N1ebieski\ICore\Cache\PageCache::class)->rememberBySlug($value)
                ?? abort(404);
        });

        $this->app['router']->bind('page_active', function ($value) {
            return \N1ebieski\ICore\Models\Page\Page::where('id', $value)
                ->active()
                ->firstOrFail();
        });

        $this->app['router']->bind('comment_active', function ($value) {
            return \N1ebieski\ICore\Models\Comment\Comment::where([
                    ['status', 1],
                    ['id', $value]
                ])->firstOrFail();
        });

        $this->app['router']->bind('category_active', function ($value) {
            return Category::where([
                    ['status', 1],
                    ['id', $value]
                ])->orWhere([
                    ['status', 1],
                    ['slug', $value]
                ])->firstOrFail();
        });

        $this->app['router']->bind('category_post_cache', function($value) {
            return App::make(\N1ebieski\ICore\Models\Category\Post\Category::class)
                ->makeCache()->rememberBySlug($value) ?? abort(404);
        });

        $this->app['router']->bind('tag_cache', function($value) {
            return App::make(\N1ebieski\ICore\Cache\TagCache::class)->rememberBySlug($value)
                ?? abort(404);
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

        //
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
        $this->app['router']->prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(function ($router) {
                 if (file_exists($override = base_path('routes') . '/vendor/icore/api.php')) {
                     require($override);
                 } else {
                     require(__DIR__ . '/../../routes/api.php');
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
                'permission:access admin'
            ])
            ->prefix('admin')
            ->as('admin.')
            ->namespace($this->namespace.'\Admin')
            ->group(function ($router) {
                foreach (glob(__DIR__ . '/../../routes/admin/*.php') as $filename){
                    if (file_exists($override = base_path('routes') . '/vendor/icore/admin/' . basename($filename))) {
                        require($override);
                    } else {
                        require($filename);
                    }
                }
            });
    }
}
