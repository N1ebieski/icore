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
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->schedule = $this->app->make(Schedule::class);

        $this->app->booted(function () {
            $this->callClearCacheSchedule();

            $this->schedule->call($this->app->make(\N1ebieski\ICore\Crons\MailingCron::class))
                ->name('MailingCron')
                ->everyThirtyMinutes();

            $this->schedule->call($this->app->make(\N1ebieski\ICore\Crons\PostCron::class))
                ->name('PostCron')
                ->everyThirtyMinutes();

            $this->schedule->call($this->app->make(\N1ebieski\ICore\Crons\Sitemap\SitemapCron::class))
                ->name('SitemapCron')
                ->daily();

            $this->schedule->command('clean:directories')
                ->name('CleanDirectories')
                ->hourly();
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
