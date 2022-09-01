<?php

namespace N1ebieski\ICore\View\Components;

use Illuminate\View\View;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Utils\MigrationUtil;
use Illuminate\Contracts\Support\Htmlable;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Cache\Session\SessionCache;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\View\Factory as ViewFactory;
use N1ebieski\ICore\ValueObjects\Post\Status as PostStatus;
use N1ebieski\ICore\ValueObjects\Comment\Status as CommentStatus;
use N1ebieski\ICore\ValueObjects\Category\Status as CategoryStatus;

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
     * @var Comment
     */
    protected $comment;

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
     * @param Category $category
     * @param Comment $comment
     * @param SessionCache $sessionCache
     * @param MigrationUtil $migrationUtil
     * @param Config $config
     * @param ViewFactory $view
     */
    public function __construct(
        Post $post,
        Category $category,
        Comment $comment,
        SessionCache $sessionCache,
        MigrationUtil $migrationUtil,
        Config $config,
        ViewFactory $view
    ) {
        $this->post = $post;
        $this->category = $category;
        $this->comment = $comment;

        $this->sessionCache = $sessionCache;

        $this->config = $config;
        $this->migrationUtil = $migrationUtil;
        $this->view = $view;
    }

    protected function verifySession(): bool
    {
        return $this->migrationUtil->contains('create_sessions_table')
            && $this->config->get('session.driver') === 'database';
    }

    /**
     *
     * @return string
     */
    public function toHtml(): string
    {
        return $this->view->make('icore::web.components.stat', [
            'countCategories' => $this->category->makeCache()->rememberCountByStatus()
                ->firstWhere('status', CategoryStatus::active()),

            'countPosts' => $this->post->makeCache()->rememberCountByStatus()
                ->firstWhere('status', PostStatus::active()),

            'countComments' => $this->comment->makeCache()->rememberCountByModelTypeAndStatus()
                ->where('status', CommentStatus::active()),

            'lastActivity' => $this->post->makeCache()->rememberLastActivity(),

            'countUsers' => $this->verifySession() ?
                $this->sessionCache->rememberCountByType()
                : null
        ])->render();
    }
}
