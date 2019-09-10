<?php

namespace N1ebieski\ICore\Http\ViewComponents;

use Illuminate\Contracts\Support\Htmlable;
use N1ebieski\ICore\Models\Newsletter;
use Illuminate\View\View;

class NewsletterComponent implements Htmlable
{
    private $newsletter;

    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
    }

    public function toHtml() : View
    {
        return view('icore::web.components.newsletter');
    }
}
