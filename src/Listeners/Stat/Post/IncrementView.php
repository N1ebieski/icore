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

use N1ebieski\ICore\Utils\MigrationUtil;
use N1ebieski\ICore\Models\Stat\Post\Stat;
use N1ebieski\ICore\ValueObjects\Stat\Slug;
use N1ebieski\ICore\Events\Inerfaces\PostEventInterface;

class IncrementView
{
    /**
     * Undocumented variable
     *
     * @var PostEventInterface
     */
    protected $event;

    /**
     *
     * @param Stat $stat
     * @param MigrationUtil $migrationUtil
     * @return void
     */
    public function __construct(protected Stat $stat, protected MigrationUtil $migrationUtil)
    {
        //
    }

    /**
     *
     * @return bool
     */
    public function verify(): bool
    {
        return $this->event->post->status->isActive()
            && $this->migrationUtil->contains('create_stats_table');
    }

    /**
     * Handle the event.
     *
     * @param  PostEventInterface  $event
     * @return void
     */
    public function handle($event): void
    {
        $this->event = $event;

        if (!$this->verify()) {
            return;
        }

        $this->stat->makeCache()
            ->rememberBySlug(Slug::VIEW)
            ->setRelations(['morph' => $this->event->post])
            ->makeService()
            ->increment();
    }
}
