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
        ], \N1ebieski\ICore\View\Composers\LayoutComposer::class);

        $this->app['view']->composer('*', \N1ebieski\ICore\View\Composers\ActiveComposer::class);
        $this->app['view']->composer('*', \N1ebieski\ICore\View\Composers\ValidComposer::class);

        $this->app['view']->composer(
            $this->app['config']->get('icore.layout') . '::admin.partials.sidebar',
            \N1ebieski\ICore\View\Composers\Admin\SidebarComposer::class
        );
    }
}
