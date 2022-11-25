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

namespace N1ebieski\ICore\Listeners\Stat\Post;

use N1ebieski\ICore\Models\Post;
use Illuminate\Events\Dispatcher;
use N1ebieski\ICore\Utils\MigrationUtil;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\Stat\Post\Stat;
use N1ebieski\ICore\ValueObjects\Stat\Slug;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\Events\Interfaces\Post\PostEventInterface;
use N1ebieski\ICore\Events\Interfaces\Post\PostCollectionEventInterface;

class IncrementView
{
    /**
     *
     * @param Stat $stat
     * @param MigrationUtil $migrationUtil
     * @return void
     */
    public function __construct(
        protected Stat $stat,
        protected MigrationUtil $migrationUtil
    ) {
        //
    }

    /**
     *
     * @param Post $post
     * @return bool
     */
    public function verify(Post $post): bool
    {
        return $post->status->isActive()
            && $this->migrationUtil->contains('copy_view_to_visit_in_stats_table');
    }

    /**
     * Handle the event.
     *
     * @param  PostEventInterface  $event
     * @return void
     */
    public function handleSingle($event): void
    {
        if (!$this->verify($event->post)) {
            return;
        }

        /** @var Stat */
        $stat = $this->stat->makeCache()->rememberBySlug(Slug::VIEW);

        $stat->setRelations(['morph' => $event->post])
            ->makeService()
            ->increment();
    }

    /**
     * Handle the event.
     *
     * @param  PostCollectionEventInterface  $event
     * @return void
     */
    public function handleGlobal($event): void
    {
        /** @var Collection */
        $morphs = $event->posts->load([
            'stats' => function (MorphToMany|Builder $query) {
                return $query->where('slug', Slug::VIEW);
            }
        ])
        ->filter(fn (Post $post) => $this->verify($post));

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
                \N1ebieski\ICore\Events\Web\Post\ShowEvent::class
            ],
            [$this::class, 'handleSingle']
        );

        $events->listen(
            [
                \N1ebieski\ICore\Events\Web\Home\IndexEvent::class,
                \N1ebieski\ICore\Events\Web\Post\IndexEvent::class,
                \N1ebieski\ICore\Events\Web\Post\SearchEvent::class,
                \N1ebieski\ICore\Events\Web\Category\Post\ShowEvent::class,
                \N1ebieski\ICore\Events\Web\Archive\Post\ShowEvent::class,
                \N1ebieski\ICore\Events\Web\Tag\Post\ShowEvent::class,
                \N1ebieski\ICore\Events\Api\Post\IndexEvent::class
            ],
            [$this::class, 'handleGlobal']
        );
    }
}
