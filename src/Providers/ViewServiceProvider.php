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
        $this->app['blade.compiler']->directive('isValid', function(string $field) {
            return "<?php echo app('Helpers\Valid')->isValid($field); ?>";
        });

        $this->app['blade.compiler']->directive('isCookie', function($input, string $output = 'active') {
            return "<?php echo app('Helpers\Active')->isCookie($input, '$output'); ?>";
        });

        $this->app['blade.compiler']->directive('isUrl', function($input, string $output = 'active') {
            return "<?php echo app('Helpers\Active')->isUrl($input, '$output'); ?>";
        });

        $this->app['blade.compiler']->directive('isRouteContains', function($input, string $output = 'active') {
            return "<?php echo app('Helpers\Active')->isRouteContains($input, '$output'); ?>";
        });

        $this->app['blade.compiler']->directive('isUrlContains', function($input, string $output = 'active') {
            return "<?php echo app('Helpers\Active')->isUrlContains($input, '$output'); ?>";
        });

        $this->app['blade.compiler']->directive('isTheme', function($input, string $output = 'active') {
            return "<?php echo app('Helpers\Active')->isTheme($input, '$output'); ?>";
        });

        $this->app['view']->composer('icore::admin.partials.sidebar',
            function($view) {
                $view->with([
                    'comments_inactive_count' => $this->app->make(\N1ebieski\ICore\Repositories\CommentRepo::class)
                        ->countInactiveByModelType()
                ]);
            });

        $this->app['view']->composer([
                'icore::web.layouts.layout',
                'icore::admin.layouts.layout'
            ], function($view) {
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
