<?php

namespace N1ebieski\ICore\Providers;

use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
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
        if ($this->app->runningInConsole()) {
            $this->commands([
                \N1ebieski\ICore\Console\Commands\RegisterSuperAdminCommand::class,
                \N1ebieski\ICore\Console\Commands\InstallCommand::class,
                \N1ebieski\ICore\Console\Commands\EnvCommand::class,
                \N1ebieski\ICore\Console\Commands\EnvTestingCommand::class,
                \N1ebieski\ICore\Console\Commands\Update\UpdateCommand::class,
                \N1ebieski\ICore\Console\Commands\Update\RollbackCommand::class
            ]);
        }
    }
}
