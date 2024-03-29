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

use N1ebieski\ICore\Utils\Migration\Interfaces\MigrationRecognizeInterface;
use N1ebieski\ICore\Models\Stat\Post\Stat;
use N1ebieski\ICore\ValueObjects\Stat\Slug;
use N1ebieski\ICore\Events\Interfaces\Post\PostEventInterface;

class IncrementVisit
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
     * @return bool
     */
    public function verify(): bool
    {
        return $this->event->post->status->isActive()
            && $this->migrationRecognize->contains('create_stats_table');
    }

    /**
     * Handle the event.
     *
     * @param  PostEventInterface  $event
     * @return void
     */
    public function handle(PostEventInterface $event): void
    {
        $this->event = $event;

        if (!$this->verify()) {
            return;
        }

        /** @var Stat */
        $stat = $this->stat->makeCache()->rememberBySlug($this->getSlug());

        $stat->setRelations(['morph' => $this->event->post])
            ->makeService()
            ->increment();
    }

    /**
     *
     * @return string
     */
    protected function getSlug(): string
    {
        if ($this->migrationRecognize->contains('copy_view_to_visit_in_stats_table')) {
            return Slug::VISIT;
        }

        return Slug::VIEW;
    }
}
