<?php

namespace N1ebieski\ICore\Filters\Traits;

use N1ebieski\ICore\Models\Category\Category;

trait HasCategory
{
    /**
     * [setCategory description]
     * @param Category $category [description]
     */
    public function setCategory(Category $category)
    {
        $this->parameters['category'] = $category;

        return $this;
    }

    /**
     *
     * @param int|null $id
     * @return void
     */
    public function filterCategory(int $id = null): void
    {
        $this->parameters['category'] = null;

        if ($id === 0) {
            $this->parameters['category'] = 0;
        }

        if ($id !== null) {
            if ($category = $this->findCategory($id)) {
                $this->setCategory($category);
            }
        }
    }

    /**
     * [findCategory description]
     * @param  int|null   $id [description]
     * @return Category     [description]
     */
    public function findCategory(int $id = null): Category
    {
        return Category::withAncestorsExceptSelf()
            ->where('id', $id)
            ->first();
    }
}
