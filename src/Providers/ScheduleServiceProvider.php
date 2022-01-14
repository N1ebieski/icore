<?php

namespace N1ebieski\ICore\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Undocumented variable
     *
     * @var Schedule
     */
    protected $schedule;

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
        $this->app->booted(function () {
            $this->schedule = $this->app->make(Schedule::class);

            $this->callClearCacheSchedule();

            $this->schedule->call($this->app->make(\N1ebieski\ICore\Crons\MailingCron::class))
                ->name('MailingCron')
                ->everyThirtyMinutes()
                ->runInBackground();

            $this->schedule->call($this->app->make(\N1ebieski\ICore\Crons\PostCron::class))
                ->name('PostCron')
                ->everyThirtyMinutes()
                ->runInBackground();

            $this->schedule->call($this->app->make(\N1ebieski\ICore\Crons\Sitemap\SitemapCron::class))
                ->name('SitemapCron')
                ->daily()
                ->runInBackground();

            $this->schedule->command('clean:directories')
                ->name('CleanDirectories')
                ->hourly()
                ->runInBackground();
        });
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function callClearCacheSchedule(): void
    {
        $hours = ceil(Config::get('cache.minutes') / 60);

        if ($hours <= 0 || $hours > 672) {
            return;
        }

        if ($hours < 24) {
            $cron = "0 */{$hours} * * *";
        } else {
            $days = ceil($hours / 24);

            $cron = "0 0 */{$days} * *";
        }

        // TODO: #37 Check is it working with runInBackground @N1ebieski
        if (Config::get('cache.default') === 'tfile') {
            $this->schedule->exec('cd storage/framework/cache && rm -r data')
                ->name('ClearCacheTfile')
                ->cron($cron);
        }

        $this->schedule->command('cache:clear')
            ->name('ClearCache')
            ->cron($cron);
    }
}
