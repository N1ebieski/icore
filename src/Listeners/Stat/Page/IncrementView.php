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
use N1ebieski\ICore\Utils\Migration\Interfaces\MigrationRecognizeInterface;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\Stat\Page\Stat;
use N1ebieski\ICore\ValueObjects\Stat\Slug;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\Events\Interfaces\Page\PageEventInterface;
use N1ebieski\ICore\Events\Interfaces\Page\PageCollectionEventInterface;

class IncrementView
{
    /**
     *
     * @param Stat $stat
     * @param MigrationRecognizeInterface $migrationRecognize
     * @return void
     */
    public function __construct(
        protected Stat $stat,
        protected MigrationRecognizeInterface $migrationRecognize
    ) {
        //
    }

    /**
     *
     * @param Page $page
     * @return bool
     */
    public function verify(Page $page): bool
    {
        return $page->status->isActive()
            && $this->migrationRecognize->contains('copy_view_to_visit_in_stats_table');
    }

    /**
     * Handle the event.
     *
     * @param  PageEventInterface  $event
     * @return void
     */
    public function handleSingle(PageEventInterface $event): void
    {
        if (!$this->verify($event->page)) {
            return;
        }

        /** @var Stat */
        $stat = $this->stat->makeCache()->rememberBySlug(Slug::VIEW);

        $stat->setRelations(['morph' => $event->page])
            ->makeService()
            ->increment();
    }

    /**
     * Handle the event.
     *
     * @param  PageCollectionEventInterface  $event
     * @return void
     */
    public function handleGlobal(PageCollectionEventInterface $event): void
    {
        /** @var Collection */
        $morphs = $event->pages->load([
            'stats' => function (MorphToMany|Builder $query) {
                return $query->where('slug', Slug::VIEW);
            }
        ])
        ->filter(fn (Page $page) => $this->verify($page));

        if ($morphs->isNotEmpty()) {
            /** @var Stat */
            $stat = $this->stat->makeCache()->rememberBySlug(Slug::VIEW);

            $stat->setRelations(['morphs' => $morphs])
                ->makeService()
                ->incrementGlobal();
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
