<?php

namespace N1ebieski\ICore\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

/**
 * [ScheduleServiceProvider description]
 */
class ScheduleServiceProvider extends ServiceProvider
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
        $this->app->booted(function() {
             $schedule = $this->app->make(Schedule::class);

             $schedule->call($this->app->make(\N1ebieski\ICore\Crons\MailingCron::class))
                ->name('MailingCron')->everyThirtyMinutes();
             $schedule->call($this->app->make(\N1ebieski\ICore\Crons\PostCron::class))
                ->name('PostCron')->everyThirtyMinutes();

             $schedule->command('queue:restart');
             $schedule->command('queue:work --daemon --stop-when-empty --tries=3');
         });
    }
}
