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

namespace N1ebieski\ICore\View\Components;

use Illuminate\View\Component;
use N1ebieski\ICore\Models\Post;
use Illuminate\Contracts\View\View;
use N1ebieski\ICore\Utils\MigrationUtil;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Cache\Session\SessionCache;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\View\Factory as ViewFactory;
use N1ebieski\ICore\ValueObjects\Post\Status as PostStatus;
use N1ebieski\ICore\ValueObjects\Comment\Status as CommentStatus;
use N1ebieski\ICore\ValueObjects\Category\Status as CategoryStatus;

class StatComponent extends Component
{
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
        protected Post $post,
        protected Category $category,
        protected Comment $comment,
        protected SessionCache $sessionCache,
        protected MigrationUtil $migrationUtil,
        protected Config $config,
        protected ViewFactory $view
    ) {
        //
    }

    /**
     *
     * @return bool
     */
    protected function verifySession(): bool
    {
        return $this->migrationUtil->contains('create_sessions_table')
            && $this->config->get('session.driver') === 'database';
    }

    /**
     *
     * @return View
     */
    public function render(): View
    {
        $countComments = $this->comment->makeCache()->rememberCountByModelTypeAndStatusAndLang()
            ->where('status', CommentStatus::active());

        return $this->view->make('icore::web.components.stat', [
            'countCategories' => $this->category->makeCache()->rememberCountByStatus()
                ->firstWhere('status', CategoryStatus::active()),

            'countPosts' => $this->post->makeCache()->rememberCountByStatus()
                ->firstWhere('status', PostStatus::active()),

            'countComments' => $countComments->map(function ($item) use ($countComments) {
                $item = clone $item;

                $item->count = $countComments->where('model', $item->model)->sum('count');

                return $item;
            })->unique(),

            'lastActivity' => $this->post->makeCache()->rememberLastActivity(),

            'countUsers' => $this->verifySession() ?
                $this->sessionCache->rememberCountByType()
                : null
        ]);
    }
}
