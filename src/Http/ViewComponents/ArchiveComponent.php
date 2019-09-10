<?php

namespace N1ebieski\ICore\Http\ViewComponents;

use Illuminate\Contracts\Support\Htmlable;
use N1ebieski\ICore\Cache\PostCache;
use Illuminate\View\View;

class ArchiveComponent implements Htmlable
{
    protected $postCache;

    public function __construct(PostCache $postCache)
    {
        $this->postCache = $postCache;
    }

    public function toHtml() : View
    {
        return view('icore::web.components.archive', [
            'archives' => $this->postCache->rememberArchives()
        ]);
    }
}
