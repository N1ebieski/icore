<?php

namespace N1ebieski\ICore\Jobs\Tag;

use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * [CachePopularTags description]
 */
class CachePopularTags implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = false;

    /**
     * Undocumented function
     * 
     * @param array|null $cats
     */
    protected $cats;

    /**
     * Create a new job instance.
     *
     * @param array|null $cats
     * @return void
     */
    public function __construct(array $cats = null)
    {
        $this->cats = $cats;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Tag $tag)
    { 
        $component = [
            'cats' => $this->cats,
            'limit' => 25
        ];

        $tag->makeCache()->putPopularByComponent(
            $tag->makeRepo()->getPopularByComponent($component),
            $component
        );
    }
}
