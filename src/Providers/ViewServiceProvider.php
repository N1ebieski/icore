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
        $this->app['blade.compiler']->directive('isValid', function (string $field) {
            return "<?php echo app('icore.helpers.valid')->isValid($field); ?>";
        });

        $this->app['blade.compiler']->directive('isCookie', function ($input, string $output = 'active') {
            return "<?php echo app('icore.helpers.active')->isCookie($input, '$output'); ?>";
        });

        $this->app['blade.compiler']->directive('isUrl', function ($input, string $output = 'active') {
            return "<?php echo app('icore.helpers.active')->isUrl($input, '$output'); ?>";
        });

        $this->app['blade.compiler']->directive('isRouteContains', function ($input, string $output = 'active') {
            return "<?php echo app('icore.helpers.active')->isRouteContains($input, '$output'); ?>";
        });

        $this->app['blade.compiler']->directive('isUrlContains', function ($input, string $output = 'active') {
            return "<?php echo app('icore.helpers.active')->isUrlContains($input, '$output'); ?>";
        });

        $this->app['blade.compiler']->directive('isTheme', function ($input, string $output = 'active') {
            return "<?php echo app('icore.helpers.active')->isTheme($input, '$output'); ?>";
        });

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
