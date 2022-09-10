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

namespace N1ebieski\ICore\View\ViewModels\Admin\Post;

use Illuminate\Http\Request;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Spatie\ViewModels\ViewModel;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Contracts\Config\Repository as Config;

class EditFullViewModel extends ViewModel
{
    /**
     *
     * @param Post $post
     * @param Category $category
     * @param User $user
     * @param Config $config
     * @param Request $request
     * @return void
     */
    public function __construct(
        public Post $post,
        protected Category $category,
        protected User $user,
        protected Config $config,
        protected Request $request
    ) {
        //
    }

    /**
     * [maxTags description]
     *
     * @return  int [return description]
     */
    public function maxTags(): int
    {
        return (int)$this->config->get('icore.post.max_tags');
    }

    /**
     * [maxCategories description]
     *
     * @return  int [return description]
     */
    public function maxCategories(): int
    {
        return (int)$this->config->get('icore.post.max_categories');
    }

    /**
     * [categoriesSelection description]
     *
     * @return  Collection  [return description]
     */
    public function categoriesSelection(): Collection
    {
        if (is_array($this->request->old('categories'))) {
            return $this->category->makeRepo()->getByIds($this->request->old('categories'));
        }

        return $this->post->categories;
    }

    /**
     * [userSelection description]
     *
     * @return  User|null  [return description]
     */
    public function userSelection(): ?User
    {
        $userId = $this->request->old('user');

        if (!is_null($userId)) {
            /**
             * @var User|null
             */
            return $this->user->find($userId);
        }

        return $this->post->user;
    }
}
