<?php

namespace N1ebieski\ICore\View\Components;

use Illuminate\View\View;
use N1ebieski\ICore\Models\Newsletter;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as ViewFactory;

class NewsletterComponent implements Htmlable
{
    /**
     * Undocumented variable
     *
     * @var Newsletter
     */
    protected $newsletter;

    /**
     * Undocumented variable
     *
     * @var ViewFactory
     */
    protected $view;

    /**
     * Undocumented function
     *
     * @param Newsletter $newsletter
     * @param ViewFactory $view
     */
    public function __construct(Newsletter $newsletter, ViewFactory $view)
    {
        $this->newsletter = $newsletter;

        $this->view = $view;
    }

    /**
     * Undocumented function
     *
     * @return View
     */
    public function toHtml(): View
    {
        return $this->view->make('icore::web.components.newsletter');
    }
}
