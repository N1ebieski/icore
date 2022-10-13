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

namespace N1ebieski\ICore\Listeners\Stat\Page;

use Illuminate\Events\Dispatcher;
use N1ebieski\ICore\Models\Page\Page;
use N1ebieski\ICore\Utils\MigrationUtil;
use N1ebieski\ICore\Models\Stat\Page\Stat;
use N1ebieski\ICore\ValueObjects\Stat\Slug;
use N1ebieski\ICore\Events\Interfaces\Page\PageEventInterface;
use N1ebieski\ICore\Events\Interfaces\Post\PageCollectionEventInterface;

class IncrementView
{
    /**
     * Undocumented variable
     *
     * @var PageEventInterface
     */
    protected $event;

    /**
     *
     * @var Stat
     */
    protected $stat;

    /**
     *
     * @param Stat $stat
     * @param MigrationUtil $migrationUtil
     * @return void
     */
    public function __construct(
        Stat $stat,
        protected MigrationUtil $migrationUtil
    ) {
        // @phpstan-ignore-next-line
        $this->stat = $stat->makeCache()->rememberBySlug(Slug::VIEW);
    }

    /**
     *
     * @param Page $page
     * @return bool
     */
    public function verify(Page $page): bool
    {
        return $page->status->isActive()
            && $this->migrationUtil->contains('copy_view_to_visit_in_stats_table');
    }

    /**
     * Handle the event.
     *
     * @param  PageEventInterface  $event
     * @return void
     */
    public function handleSingle($event): void
    {
        if (!$this->verify($event->page)) {
            return;
        }

        $this->stat->setRelations(['morph' => $event->page])
            ->makeService()
            ->increment();
    }

    /**
     * Handle the event.
     *
     * @param  PageCollectionEventInterface  $event
     * @return void
     */
    public function handleGlobal($event): void
    {
        /** @var array */
        $ids = $event->pages->filter(fn (Page $page) => $this->verify($page))
            ->pluck('id')
            ->toArray();

        if (count($ids) > 0) {
            $this->stat->makeService()->incrementGlobal($ids);
        }
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
                \N1ebieski\ICore\Events\Web\Page\ShowEvent::class
            ],
            [$this::class, 'handleSingle']
        );
    }
}
