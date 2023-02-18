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

namespace N1ebieski\ICore\Crons\AutoTranslate;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Bus\Dispatcher as Job;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Jobs\AutoTranslate\AutoTranslateJob;
use N1ebieski\ICore\Models\Interfaces\AutoTranslateInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\ICore\Crons\AutoTranslate\Builder\Interfaces\BuilderInterface;

class Director
{
    /**
     *
     * @param Carbon $carbon
     * @param Config $config
     * @param Job $job
     * @param App $app
     * @return void
     */
    public function __construct(
        protected Carbon $carbon,
        protected Config $config,
        protected Job $job,
        protected App $app
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function build(BuilderInterface $builder): void
    {
        $builder->chunkCollection(function (Collection $collection) {
            $collection->each(function (AutoTranslateInterface $model) {
                $this->addToQueue($model);
            });
        }, $this->getCheckTimestamp());
    }

    /**
     *
     * @param AutoTranslateInterface $model
     * @return void
     * @throws BindingResolutionException
     */
    protected function addToQueue(AutoTranslateInterface $model): void
    {
        $this->job->dispatch($this->app->make(AutoTranslateJob::class, [
            'model' => $model
        ]));
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getCheckTimestamp(): string
    {
        return $this->carbon->now()->subDays(
            $this->config->get('icore.auto_translate.check_days')
        );
    }
}
