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

                $this->callClearCacheSchedule($schedule);

                $schedule->call($this->app->make(\N1ebieski\ICore\Crons\AutoTranslate\AutoTranslateCron::class))
                    ->name('AutoTranslateCron')
                    ->everyFifteenMinutes();

                $schedule->call($this->app->make(\N1ebieski\ICore\Crons\MailingCron::class))
                    ->name('MailingCron')
                    ->everyThirtyMinutes();

                $schedule->call($this->app->make(\N1ebieski\ICore\Crons\PostCron::class))
                    ->name('PostCron')
                    ->everyThirtyMinutes();

                $schedule->call($this->app->make(\N1ebieski\ICore\Crons\Sitemap\SitemapCron::class))
                    ->name('SitemapCron')
                    ->daily();

                $schedule->command('clean:directories')
                    ->name('CleanDirectories')
                    ->hourly();
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
            $schedule->exec('cd storage/framework/cache && rm -r data')
                ->name('ClearCacheTfile')
                ->cron($cron);
        }

        $schedule->command('cache:clear')
            ->name('ClearCache')
            ->cron($cron);
    }
}
