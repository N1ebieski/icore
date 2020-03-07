<?php

namespace N1ebieski\ICore\Http\ViewComponents;

use Illuminate\Contracts\Support\Htmlable;
use N1ebieski\ICore\Models\Newsletter;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\View;

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
    public function toHtml() : View
    {
        return $this->view->make('icore::web.components.newsletter');
    }
}
