<?php

namespace N1ebieski\ICore\Events\Web\Page;

use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ShowEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * [public description]
     * @var Page
     */
    public $page;

    /**
     * Undocumented variable
     *
     * @var Page
     */
    public $morph;

    /**
     * Create a new event instance.
     *
     * @param Page         $page    [description]
     * @return void
     */
    public function __construct(Page $page)
    {
        $this->page = $page;

        $this->morph = $page;
    }
}
