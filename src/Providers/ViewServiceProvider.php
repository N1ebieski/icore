<?php

namespace N1ebieski\ICore\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * [ViewServiceProvider description]
 */
class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['blade.compiler']->directive('pushonce', function ($expression) {
            $domain = explode('.', trim(substr($expression, 1, -1)));

            $push_name = $domain[0];
            $push_sub = $domain[1];
            $isDisplayed = '__pushonce_'.$push_name.'_'.$push_sub;

            return "<?php if(!isset(\$__env->{$isDisplayed})): \$__env->{$isDisplayed} = true; \$__env->startPush('{$push_name}'); ?>";
        });

        $this->app['blade.compiler']->directive('endpushonce', function ($expression) {
            return '<?php $__env->stopPush(); endif; ?>';
        });

        $this->app['view']->composer([
            $this->app['config']->get('icore.layout') . '::web.layouts.layout',
            $this->app['config']->get('icore.layout') . '::admin.layouts.layout',
        ], function ($view) {
            $view->with($this->app->make(\N1ebieski\ICore\View\ViewModels\LayoutViewModel::class)->toArray());
        });

        $this->app['view']->composer('*', function ($view) {
            $view->with($this->app->make(\N1ebieski\ICore\View\ViewModels\ActiveViewModel::class)->toArray());
            $view->with($this->app->make(\N1ebieski\ICore\View\ViewModels\ValidViewModel::class)->toArray());
        });

        $this->app['view']->composer(
            $this->app['config']->get('icore.layout') . '::admin.partials.sidebar',
            function ($view) {
                $view->with([
                    'comments_inactive_count' => $this->app->make(\N1ebieski\ICore\Repositories\CommentRepo::class)
                        ->countInactiveByModelType(),
                    'comments_reported_count' => $this->app->make(\N1ebieski\ICore\Repositories\CommentRepo::class)
                        ->countReportedByModelType()
                ]);
            }
        );

        $this->app['view']->composer([
                $this->app['config']->get('icore.layout') . '::web.layouts.layout',
                $this->app['config']->get('icore.layout') . '::admin.layouts.layout'
            ], function ($view) {
                $view->with(array_replace_recursive([
                    'title' => array(),
                    'desc' => array(),
                    'keys' => array(),
                    'index' => 'index',
                    'follow' => 'follow',
                    'og' => [
                        'title' => null,
                        'desc' => null,
                        'image' => null,
                        'type' => null
                    ]
                ], $view->getData()));
            });
    }
}
