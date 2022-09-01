<?php

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
     * [$category description]
     *
     * @var Category
     */
    protected $category;

    /**
     * [$post description]
     *
     * @var Post
     */
    public $post;

    /**
     * Undocumented variable
     *
     * @var User
     */
    protected $user;

    /**
     * [$config description]
     *
     * @var Config
     */
    protected $config;

    /**
     *
     * @var Request
     */
    private $request;

    /**
     * Undocumented function
     *
     * @param Category $category
     * @param Post $post
     * @param User $user
     * @param Config $config
     * @param Request $request
     */
    public function __construct(
        Category $category,
        Post $post,
        User $user,
        Config $config,
        Request $request
    ) {
        $this->category = $category;
        $this->post = $post;
        $this->user = $user;

        $this->config = $config;
        $this->request = $request;
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
