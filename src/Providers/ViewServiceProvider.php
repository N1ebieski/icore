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

        /** @var \N1ebieski\ICore\View\Directives\PushOnceDirective */
        $pushOnceDirective = $this->app->make(\N1ebieski\ICore\View\Directives\PushOnceDirective::class);

        Blade::directive('pushonce', $pushOnceDirective);

        /** @var \N1ebieski\ICore\View\Directives\EndPushOnceDirective */
        $endPushOnceDirective = $this->app->make(\N1ebieski\ICore\View\Directives\EndPushOnceDirective::class);

        Blade::directive('endpushonce', $endPushOnceDirective);

        $bladeCompiler = new \Illuminate\View\Compilers\BladeCompiler(
            $this->app->make(\Illuminate\Filesystem\Filesystem::class),
            Config::get('view.compiled')
        );

        $bladeCompiler->componentNamespace('N1ebieski\\ICore\\View\\Components', 'icore');

        /** @var \N1ebieski\ICore\View\Directives\RenderDirective */
        $renderDirective = $this->app->make(\N1ebieski\ICore\View\Directives\RenderDirective::class, [
            'bladeCompiler' => $bladeCompiler
        ]);

        Blade::directive('render', $renderDirective);

        /** @var \N1ebieski\ICore\View\Composers\LayoutComposer */
        $layoutComposer = $this->app->make(\N1ebieski\ICore\View\Composers\LayoutComposer::class);

        // Composers

        View::composer([
            Config::get('icore.layout') . '::web.layouts.layout',
            Config::get('icore.layout') . '::admin.layouts.layout',
            'file-manager::fmButton',
        ], $layoutComposer::class);

        /** @var \N1ebieski\ICore\View\Composers\ActiveComposer */
        $activeComposer = $this->app->make(\N1ebieski\ICore\View\Composers\ActiveComposer::class);

        View::composer('*', $activeComposer::class);

        /** @var \N1ebieski\ICore\View\Composers\ValidComposer */
        $validComposer = $this->app->make(\N1ebieski\ICore\View\Composers\ValidComposer::class);

        View::composer('*', $validComposer::class);

        /** @var \N1ebieski\ICore\View\Composers\Admin\SidebarComposer */
        $sidebarComposer = $this->app->make(\N1ebieski\ICore\View\Composers\Admin\SidebarComposer::class);

        View::composer(
            Config::get('icore.layout') . '::admin.partials.sidebar',
            $sidebarComposer::class
        );

        /** @var \N1ebieski\ICore\View\Composers\Admin\NavbarComposer */
        $navbarComposer = $this->app->make(\N1ebieski\ICore\View\Composers\Admin\NavbarComposer::class);

        View::composer(
            Config::get('icore.layout') . '::admin.partials.nav',
            $navbarComposer::class
        );
    }
}
