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

namespace N1ebieski\ICore\Listeners;

use Illuminate\Events\Dispatcher;
use Illuminate\Contracts\Bus\Dispatcher as Job;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Jobs\AutoTranslate\AutoTranslateJob;
use N1ebieski\ICore\Models\Interfaces\AutoTranslateInterface;
use N1ebieski\ICore\Events\Interfaces\AutoTranslateEventInterface;

class AutoTranslate
{
    /**
     *
     * @var AutoTranslateInterface
     */
    protected AutoTranslateInterface $model;

    /**
     *
     * @param Job $job
     * @param App $app
     * @param Config $config
     * @return void
     */
    public function __construct(
        protected Job $job,
        protected App $app,
        protected Config $config
    ) {
        //
    }

    /**
     *
     * @return bool
     */
    protected function verify(): bool
    {
        return count($this->config->get('icore.multi_langs')) > 1
            && $this->model->auto_translate->isActive()
            && $this->model->langs->isNotEmpty();
    }

    /**
     * Handle the event.
     *
     * @param  AutoTranslateEventInterface  $event
     * @return void
     */
    public function handle(AutoTranslateEventInterface $event): void
    {
        $this->model = $event->model;

        if (!$this->verify()) {
            return;
        }

        $this->job->dispatch($this->app->make(AutoTranslateJob::class, [
            'model' => $event->model
        ]));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Dispatcher  $events
     * @return void
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            [
                \N1ebieski\ICore\Events\Admin\Post\StoreEvent::class,
                \N1ebieski\ICore\Events\Admin\Post\UpdateEvent::class,
                \N1ebieski\ICore\Events\Admin\Post\UpdateFullEvent::class,
                \N1ebieski\ICore\Events\Admin\Page\StoreEvent::class,
                \N1ebieski\ICore\Events\Admin\Page\UpdateEvent::class,
                \N1ebieski\ICore\Events\Admin\Page\UpdateFullEvent::class,
                \N1ebieski\ICore\Events\Admin\Mailing\StoreEvent::class,
                \N1ebieski\ICore\Events\Admin\Mailing\UpdateEvent::class,
                \N1ebieski\ICore\Events\Admin\Category\StoreEvent::class,
                \N1ebieski\ICore\Events\Admin\Category\UpdateEvent::class,
            ],
            [$this::class, 'handle']
        );
    }
}
