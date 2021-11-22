<?php

namespace N1ebieski\ICore\View\ViewModels\Admin\Post;

use Illuminate\Http\Request;
use Spatie\ViewModels\ViewModel;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Models\Category\Post\Category;
use Illuminate\Contracts\Config\Repository as Config;

class CreateViewModel extends ViewModel
{
    /**
     * [$category description]
     *
     * @var Category
     */
    protected $category;

    /**
     * [$config description]
     *
     * @var Config
     */
    protected $config;

    /**
     * [__construct description]
     *
     * @param   Category  $category  [$category description]
     * @param   Config    $config    [$config description]
     * @param   Request   $request   [$request description]
     */
    public function __construct(Category $category, Config $config, Request $request)
    {
        $this->category = $category;

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
     * @return  Collection|null  [return description]
     */
    public function categoriesSelection(): ?Collection
    {
        if ($this->request->old('categories')) {
            return $this->category->makeRepo()->getByIds($this->request->old('categories'));
        }

        return null;
    }
}
