<?php

namespace N1ebieski\ICore\Listeners\Stat\Post;

use N1ebieski\ICore\Utils\MigrationUtil;
use N1ebieski\ICore\Models\Stat\Post\Stat;

class IncrementView
{
    /**
     * Undocumented variable
     *
     * @var object
     */
    protected $event;

    /**
     * Undocumented variable
     *
     * @var Stat
     */
    protected $stat;

    /**
     * Undocumented variable
     *
     * @var MigrationUtil
     */
    protected $migrationUtil;

    /**
     * Undocumented function
     *
     * @param MigrationUtil $util
     */
    public function __construct(Stat $stat, MigrationUtil $migrationUtil)
    {
        $this->stat = $stat;

        $this->migrationUtil = $migrationUtil;
    }

    /**
     *
     * @return bool
     */
    public function verify() : bool
    {
        return $this->event->post->isActive()
            && $this->migrationUtil->contains('create_stats_table');
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event) : void
    {
        $this->event = $event;

        if (!$this->verify()) {
            return;
        }

        $this->stat->makeCache()
            ->rememberBySlug($this->stat::VIEW)
            ->setRelations(['morph' => $this->event->post])
            ->makeService()
            ->increment();
    }
}
