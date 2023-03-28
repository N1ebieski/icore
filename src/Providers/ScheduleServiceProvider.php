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
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);

                $resync = Config::get('icore.schedule.resync');

                $this->callClearCacheSchedule($schedule);

                $schedule->call($this->app->make(\N1ebieski\ICore\Crons\MailingCron::class))
                    ->name('MailingCron')
                    ->hourlyAt((int)$resync);

                $schedule->call($this->app->make(\N1ebieski\ICore\Crons\PostCron::class))
                    ->name('PostCron')
                    ->hourlyAt((int)$resync);

                $schedule->call($this->app->make(\N1ebieski\ICore\Crons\Sitemap\SitemapCron::class))
                    ->name('SitemapCron')
                    ->daily("00:{$resync}");

                $schedule->command('clean:directories')
                    ->name('CleanDirectories')
                    ->hourlyAt((int)$resync);
            });
        }
    }

    /**
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function callClearCacheSchedule(Schedule $schedule): void
    {
        $hours = ceil(Config::get('cache.minutes') / 60);

        $resync = (int)Config::get('icore.schedule.resync');

        if ($hours <= 0 || $hours > 672) {
            return;
        }

        if ($hours < 24) {
            $cron = "{$resync} */{$hours} * * *";
        } else {
            $days = ceil($hours / 24);

            $cron = "{$resync} 0 */{$days} * *";
        }

        // TODO: #37 Check is it working with runInBackground @N1ebieski
        if (Config::get('cache.default') === 'tfile') {
            $schedule->exec('cd storage/framework/cache && rm -r data')
                ->name('ClearCacheTfile')
                ->cron($cron);
        }

        $schedule->command('cache:clear')
            ->name('ClearCache')
            ->cron($cron);
    }
}
