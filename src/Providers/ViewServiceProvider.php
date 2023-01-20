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

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        Blade::componentNamespace('N1ebieski\\ICore\\View\\Components', 'icore');

        // Directives

        Blade::directive('pushonce', $this->app->make(\N1ebieski\ICore\View\Directives\PushOnceDirective::class));

        Blade::directive('endpushonce', $this->app->make(\N1ebieski\ICore\View\Directives\EndPushOnceDirective::class));

        $bladeCompiler = new \Illuminate\View\Compilers\BladeCompiler(
            $this->app->make(\Illuminate\Filesystem\Filesystem::class),
            Config::get('view.compiled')
        );

        $bladeCompiler->componentNamespace('N1ebieski\\ICore\\View\\Components', 'icore');

        Blade::directive('render', $this->app->make(\N1ebieski\ICore\View\Directives\RenderDirective::class, [
            'bladeCompiler' => $bladeCompiler
        ]));

        // Composers

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

        View::composer(
            Config::get('icore.layout') . '::admin.partials.nav',
            \N1ebieski\ICore\View\Composers\Admin\NavbarComposer::class
        );
    }
}
