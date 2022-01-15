<?php

namespace N1ebieski\ICore\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

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
        Blade::directive('pushonce', function ($expression) {
            $domain = explode('.', trim(substr($expression, 1, -1)));

            $push_name = $domain[0];
            $push_sub = $domain[1];
            $isDisplayed = '__pushonce_' . $push_name . '_' . $push_sub;

            return "<?php if(!isset(\$__env->{$isDisplayed})): \$__env->{$isDisplayed} = true; \$__env->startPush('{$push_name}'); ?>";
        });

        Blade::directive('endpushonce', function ($expression) {
            return '<?php $__env->stopPush(); endif; ?>';
        });

        View::composer([
            Config::get('icore.layout') . '::web.layouts.layout',
            Config::get('icore.layout') . '::admin.layouts.layout',
            'file-manager::fmButton',
        ], \N1ebieski\ICore\View\Composers\LayoutComposer::class);

        View::composer('*', \N1ebieski\ICore\View\Composers\ActiveComposer::class);
        View::composer('*', \N1ebieski\ICore\View\Composers\ValidComposer::class);

        View::composer(
            Config::get('icore.layout') . '::admin.partials.sidebar',
            \N1ebieski\ICore\View\Composers\Admin\SidebarComposer::class
        );
    }
}
