<?php

namespace N1ebieski\ICore\View\Components;

use Illuminate\View\View;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Cache\SessionCache;
use N1ebieski\ICore\Utils\MigrationUtil;
use Illuminate\Contracts\Support\Htmlable;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\View\Factory as ViewFactory;

class StatComponent implements Htmlable
{
    /**
     * Undocumented variable
     *
     * @var Post
     */
    protected $post;

    /**
     * Undocumented variable
     *
     * @var Category
     */
    protected $category;

    /**
     * Undocumented variable
     *
     * @var SessionCache
     */
    protected $sessionCache;

    /**
     * Undocumented variable
     *
     * @var MigrationUtil
     */
    protected $migrationUtil;

    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented variable
     *
     * @var ViewFactory
     */
    protected $view;

    /**
     * Undocumented function
     *
     * @param Post $post
     * @param ViewFactory $view
     */
    public function __construct(
        Post $post,
        Category $category,
        SessionCache $sessionCache,
        MigrationUtil $migrationUtil,
        Config $config,
        ViewFactory $view
    ) {
        $this->post = $post;
        $this->category = $category;

        $this->sessionCache = $sessionCache;

        $this->config = $config;
        $this->migrationUtil = $migrationUtil;
        $this->view = $view;
    }

    protected function verifySession() : bool
    {
        return $this->migrationUtil->contains('create_sessions_table')
            && $this->config->get('session.driver') === 'database';
    }

    /**
     * Undocumented function
     *
     * @return View
     */
    public function toHtml() : View
    {
        return $this->view->make('icore::web.components.stat', [
            'category' => $this->category,
            'post' => $this->post,
            'countCategories' => $this->category->makeCache()->rememberCountByStatus(),
            'countPosts' => $this->post->makeCache()->rememberCountByStatus(),
            'lastActivity' => $this->post->makeCache()->rememberLastActivity(),
            'countUsers' => $this->verifySession() ?
                $this->sessionCache->rememberCountByType()
                : null
        ]);
    }
}
