<?php

namespace N1ebieski\ICore\Providers;

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
     * Undocumented function
     *
     * @return void
     */
    protected function prepareClearCacheSchedule() : void
    {
        $hours = ceil($this->app['config']->get('cache.minutes')/60);

        if ($hours <= 0 || $hours > 672) {
            return;
        }

        if ($hours < 24) {
            $cron = "1 */{$hours} * * *";
        } else {
            $days = ceil($hours/24);

            $cron = "1 0 */{$days} * *";
        }

        $this->schedule->command('cache:clear')
            ->name('ClearCache')
            ->cron($cron)
            ->runInBackground();
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

            $this->prepareClearCacheSchedule();

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

            // $this->schedule->call($this->app->make(\N1ebieski\ICore\Crons\Tag\Post\PopularTagsCron::class))
            //     ->name('Post.PopularTagsCron')
            //     ->daily()
            //     ->runInBackground();
        });
    }
}
